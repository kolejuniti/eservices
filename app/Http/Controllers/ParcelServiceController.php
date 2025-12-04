<?php

namespace App\Http\Controllers;

use App\Models\Parcel;
use App\Models\Courier;
use App\Http\Requests\StoreParcelWithRecipientRequest;
use App\Http\Requests\StoreParcelWithoutRecipientRequest;
use App\Http\Requests\UpdateParcelClaimRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;

class ParcelServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard()
    {
        // Get statistics
        $totalParcelsToday = Parcel::whereDate('created_at', today())->count();
        $totalParcelsThisWeek = Parcel::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $totalParcelsThisMonth = Parcel::whereMonth('created_at', now()->month)->count();
        
        $unclaimedParcels = Parcel::where('status', 1)->count();
        $claimedParcels = Parcel::where('status', 2)->count();
        
        $pendingCOD = Parcel::where('status', 1)->sum('cod_amount');
        
        // Recent parcels (last 10)
        $recentParcels = Parcel::with('courier')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('parcel.dashboard', compact(
            'totalParcelsToday',
            'totalParcelsThisWeek',
            'totalParcelsThisMonth',
            'unclaimedParcels',
            'claimedParcels',
            'pendingCOD',
            'recentParcels'
        ));
    }

    public function courier()
    {
        $couriers = Courier::all();

        return view('parcel.courier', compact('couriers'));
    }

    public function addCourier(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:couriers,name'],
        ]);

        Courier::create([
            'name' => $request->input('name'),
        ]);

        return redirect()->back()->with('success', 'New courier type successfully add into the system.');
    }

    public function formWithRecipient()
    {
        $couriers = Courier::all();

        return view('parcel.registerwithrecipient', compact('couriers'));
    }

    public function searchRecipient(Request $request)
    {
        $search = trim($request->query('search'));

        // Fixed SQL injection vulnerability by using parameter binding
        $users = DB::table('eduhub.users')
            ->select('ic', 'name', 'no_staf AS id')
            ->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('ic', 'LIKE', '%' . $search . '%')
                    ->orWhere('no_staf', 'LIKE', '%' . $search . '%');
            })
            ->get();

        $students = DB::table('eduhub.students')
            ->select('ic', 'name', 'no_matric AS id')
            ->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('ic', 'LIKE', '%' . $search . '%')
                    ->orWhere('no_matric', 'LIKE', '%' . $search . '%');
            })
            ->get();

        $recipients = $users->merge($students)->unique('ic')->values(); // reset keys

        return response()->json($recipients);
    }

    public function recipientDetails(Request $request)
    {
        $ic = $request->input('ic');

        $users = DB::table('eduhub.users')
            ->select('name', 'ic', 'no_staf AS id', DB::raw("'-' AS phone"))
            ->where('ic', '=', $ic)
            ->first();

        $students = DB::table('eduhub.students as s')
            ->leftJoin('eduhub.tblstudent_personal as tp', 's.ic', '=', 'tp.student_ic')
            ->select(
                's.name',
                's.ic',
                's.no_matric as id', 'tp.no_tel as phone'
            )
            ->where('s.ic', '=', $ic)
            ->first();

        $recipient = $users ?: $students;

        return response()->json(['recipient' => $recipient]);
    }

    public function registerParcelWithRecipient(StoreParcelWithRecipientRequest $request)
    {
        DB::beginTransaction();
        
        try {
            Parcel::create([
                'ic' => $request->input('ic'),
                'courier_id' => $request->input('courier'),
                'serial_number' => $request->input('serial_number'),
                'parcel_size' => $request->input('parcel_size'),
                'amount' => Parcel::calculateAmount($request->input('parcel_size')),
                'cod_id' => $request->has('cod') && $request->input('cod') ? true : false,
                'cod_amount' => $request->input('cod_amount') ?? 0,
                'notes' => $request->input('notes'),
                'status' => 1,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Parcel telah berjaya didaftarkan didalam sistem.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error registering parcel with recipient: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);
            return redirect()->back()
                ->with('alert', 'Ralat berlaku semasa mendaftar parcel: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function formWithoutRecipient()
    {
        $couriers = Courier::all();

        return view('parcel.registerwithoutrecipient', compact('couriers'));
    }

    public function registerParcelWithoutRecipient(StoreParcelWithoutRecipientRequest $request)
    {
        DB::beginTransaction();
        
        try {
            Parcel::create([
                'recipient_name' => $request->input('recepient_name'),
                'sender_name' => $request->input('sender_name'),
                'courier_id' => $request->input('courier'),
                'tracking_number' => $request->input('tracking_number'),
                'parcel_size' => $request->input('parcel_size'),
                'amount' => Parcel::calculateAmount($request->input('parcel_size')),
                'cod_id' => $request->input('cod') ? 1 : 0,
                'cod_amount' => $request->input('cod_amount') ?? 0,
                'notes' => $request->input('notes'),
                'status' => 1,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Parcel telah berjaya didaftarkan didalam sistem.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('alert', 'Ralat berlaku semasa mendaftar parcel.');
        }
    }

    public function claimWithRecipient(Request $request)
    {
        $search = $request->input('search');

        $recipients = collect(); // default empty collection

        if ($search) {
            $users = DB::table('eduhub.users')
                ->join('parcels', 'eduhub.users.ic', '=', 'parcels.ic')
                ->select(
                    'eduhub.users.name AS recipient_name',
                    'eduhub.users.ic AS ic',
                    DB::raw('COUNT(parcels.id) AS total_parcels'),
                    DB::raw('SUM(CASE WHEN parcels.cod_amount > 0 THEN 1 ELSE 0 END) AS cod_parcels')
                )
                ->where(function ($query) use ($search) {
                    $query->where('eduhub.users.ic', 'LIKE', '%' . $search . '%')
                        ->orWhere('eduhub.users.name', 'LIKE', '%' . $search . '%')
                        ->orWhere('eduhub.users.no_staf', 'LIKE', '%' . $search . '%');
                })
                ->whereIN('parcels.status', [1,2]) // Only include parcels with status 1
                ->groupBy('eduhub.users.ic', 'eduhub.users.name', 'eduhub.users.no_staf')
                ->get();

            $students = DB::table('eduhub.students')
                ->join('parcels', 'eduhub.students.ic', '=', 'parcels.ic')
                ->select(
                    'eduhub.students.name AS recipient_name',
                    'eduhub.students.ic AS ic',
                    DB::raw('COUNT(parcels.id) AS total_parcels'),
                    DB::raw('SUM(CASE WHEN parcels.cod_amount > 0 THEN 1 ELSE 0 END) AS cod_parcels')
                )
                ->where(function ($query) use ($search) {
                    $query->where('eduhub.students.ic', 'LIKE', '%' . $search . '%')
                        ->orWhere('eduhub.students.name', 'LIKE', '%' . $search . '%')
                        ->orWhere('eduhub.students.no_matric', 'LIKE', '%' . $search . '%');
                })
                ->whereIN('parcels.status', [1,2]) // Only include parcels with status 1
                ->groupBy('eduhub.students.ic', 'eduhub.students.name', 'eduhub.students.no_matric')
                ->get();

            $recipients = $users->merge($students)->unique('ic')->values(); // reset keys
        }

        return view('parcel.claimwithrecipient', compact('recipients', 'search'));
    }

    public function recipientParcelDetails(Request $request, $encryptedIc)
    {
        try {
            $ic = Crypt::decryptString($encryptedIc);

            $users = DB::table('eduhub.users')
                ->select('name', 'ic') // Select only the 'name' column
                ->where('ic', '=', $ic)
                ->first(); // Use first() to get one result

            $students = DB::table('eduhub.students')
                ->select('name', 'ic') // Select only the 'name' column
                ->where('ic', '=', $ic)
                ->first(); // Use first() to get one result

            // Merge the results, giving priority to users
            $recipient = $users ?: $students;

            $parcels = DB::table('parcels')
                ->join('couriers', 'parcels.courier_id', '=', 'couriers.id')
                ->select('parcels.*', 'couriers.name as courier_name')
                ->where('parcels.ic', $ic)
                ->orderByDesc('parcels.created_at')
                ->get();

            $total_cod = DB::table('parcels')
                ->where('ic', $ic)
                ->sum('cod_amount');

            return view('parcel.recipientparceldetails', compact('parcels', 'total_cod', 'recipient'));
        
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            abort(403, 'Invalid IC or tampered link.');
        }
    }

    public function claimWithoutRecipient(Request $request)
    {
        // Use user input if available, otherwise default to last 7 days
        $start_date = $request->input('start_date') 
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : Carbon::now()->subDays(7)->startOfDay();

        $end_date = $request->input('end_date') 
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : Carbon::now()->endOfDay();

        $parcels = DB::table('parcels')
            ->join('couriers', 'parcels.courier_id', '=', 'couriers.id')
            ->select('parcels.*', 'couriers.name as courier_name')
            ->where(function ($query) {
                $query->whereNotNull('parcels.recipient_name')
                    ->orWhere(function ($q) {
                        $q->whereNull('parcels.recipient_name')
                            ->whereNull('parcels.ic');
                    });
            })
            ->whereBetween(DB::raw("CAST(parcels.created_at AS DATE)"), [$start_date, $end_date])
            ->orderByDesc('parcels.created_at')
            ->get();

        $total_cod = DB::table('parcels')
            ->where(function ($query) {
                $query->whereNotNull('parcels.recipient_name')
                    ->orWhere(function ($q) {
                        $q->whereNull('parcels.recipient_name')
                            ->whereNull('parcels.ic');
                    });
            })
            ->whereBetween(DB::raw("CAST(parcels.created_at AS DATE)"), [$start_date, $end_date])
            ->sum('cod_amount');

        return view('parcel.claimwithoutrecipient', compact('parcels', 'total_cod', 'start_date', 'end_date'));
    }

    public function claimWithoutRecipientUpdate(UpdateParcelClaimRequest $request, $id)
    {
        DB::beginTransaction();
        
        try {
            Parcel::where('id', $id)->update([
                'cod_amount' => 0,
                'status' => 2,
                'updated_at' => $request->input('pickup_date'),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Status parcel telah berjaya dikemaskini.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('alert', 'Ralat berlaku semasa kemaskini status parcel.');
        }
    }

    public function claimWithRecipientUpdate(UpdateParcelClaimRequest $request, $id)
    {
        DB::beginTransaction();
        
        try {
            Parcel::where('id', $id)->update([
                'cod_amount' => 0,
                'status' => 2,
                'updated_at' => $request->input('pickup_date'),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Status parcel telah berjaya dikemaskini.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('alert', 'Ralat berlaku semasa kemaskini status parcel.');
        }
    }

    public function parcelReports(Request $request)
    {
        // Use user input if available, otherwise default to last 7 days
        $start_date = $request->input('start_date') 
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : Carbon::now()->subDays(0)->startOfDay();

        $end_date = $request->input('end_date') 
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : Carbon::now()->endOfDay();
        
        $status = $request->input('status');
            
        $query = DB::table('parcels')
            ->join('couriers', 'parcels.courier_id', '=', 'couriers.id')
            ->select('parcels.*', 'couriers.name as courier_name')
            ->whereBetween(DB::raw("CAST(parcels.created_at AS DATE)"), [$start_date, $end_date]);
        
        // Filter by status if provided
        if ($status !== null && $status !== '') {
            $query->where('parcels.status', $status);
        }
        
        $parcels = $query->orderByDesc('parcels.created_at')->get();

        return view('parcel.claimreport', compact('parcels', 'start_date', 'end_date', 'status'));
    }
}

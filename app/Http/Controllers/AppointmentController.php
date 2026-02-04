class AppointmentController extends Controller {

    public function index(){
        $appointments = Appointment::where('user_id',auth()->id())->get();
        return view('appointments.index',compact('appointments'));
    }

    public function create($ticketId){
        return view('appointments.create',compact('ticketId'));
    }

    public function store(Request $r){
        Appointment::create([
            'ticket_id'=>$r->ticket_id,
            'user_id'=>auth()->id(),
            'date'=>$r->date,
            'time'=>$r->time,
            'type'=>$r->type,
            'status'=>'Programada'
        ]);

        return redirect('/appointments');
    }

    public function show($id){
        $appointment = Appointment::findOrFail($id);
        return view('appointments.show',compact('appointment'));
    }
}

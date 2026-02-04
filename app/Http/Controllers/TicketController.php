class TicketController extends Controller {

    public function create() {
        return view('tickets.create');
    }

    public function store(Request $r) {
        Ticket::create([
            'user_id' => auth()->id(),
            'subject' => $r->subject,
            'description' => $r->description,
            'priority' => $r->priority,
            'status' => 'Nuevo'
        ]);

        return redirect('/tickets');
    }

    public function index() {
        $tickets = Ticket::where('user_id',auth()->id())->get();
        return view('tickets.index',compact('tickets'));
    }

    public function show($id) {
        $ticket = Ticket::with('messages','report')->findOrFail($id);
        return view('tickets.show',compact('ticket'));
    }

    public function addMessage(Request $r,$id) {
        TicketMessage::create([
            'ticket_id'=>$id,
            'user_id'=>auth()->id(),
            'message'=>$r->message
        ]);
        return back();
    }
}


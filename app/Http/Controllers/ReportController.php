class ReportController extends Controller {

    public function show($ticketId){
        $ticket = Ticket::findOrFail($ticketId);
        $logs = ServiceLog::where('ticket_id',$ticketId)->get();

        return view('reports.show',compact('ticket','logs'));
    }

    public function pdf($ticketId){
        $ticket = Ticket::findOrFail($ticketId);
        $logs = ServiceLog::where('ticket_id',$ticketId)->get();

        $pdf = PDF::loadView('reports.pdf',compact('ticket','logs'));
        return $pdf->download('reporte_servicio_'.$ticketId.'.pdf');
    }
}

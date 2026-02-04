class Appointment extends Model {

    protected $fillable = [
        'ticket_id',
        'user_id',
        'technician_id',
        'date',
        'time',
        'type',
        'status',
        'notes'
    ];

    public function ticket(){
        return $this->belongsTo(Ticket::class);
    }

    public function client(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function technician(){
        return $this->belongsTo(User::class,'technician_id');
    }
}

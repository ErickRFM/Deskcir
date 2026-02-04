class ServiceLog extends Model {

    protected $fillable = [
        'ticket_id',
        'user_id',
        'description'
    ];

    public function ticket(){
        return $this->belongsTo(Ticket::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}

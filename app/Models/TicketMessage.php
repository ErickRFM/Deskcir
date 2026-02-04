class TicketMessage extends Model {
    protected $fillable = [
        'ticket_id','user_id','message'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}

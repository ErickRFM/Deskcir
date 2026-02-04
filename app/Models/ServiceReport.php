class ServiceReport extends Model {
    protected $fillable = [
        'ticket_id',
        'diagnosis',
        'actions_taken',
        'recommendations',
        'closed_at'
    ];

    public function ticket() {
        return $this->belongsTo(Ticket::class);
    }
}

class Ticket extends Model {
    protected $fillable = [
        'user_id','subject','description',
        'status','priority','assigned_to'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function messages() {
        return $this->hasMany(TicketMessage::class);
    }

    public function report() {
        return $this->hasOne(ServiceReport::class);
    }
}

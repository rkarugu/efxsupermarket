<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;


class Feedback extends Model
{
    public $table = "feedbacks";

     public function getAssociateUserForFeedback() {
        return $this->belongsTo('App\Model\User', 'user_id');
    }

     public function getAssociateRestroForFeedback() {
        return $this->belongsTo('App\Model\Restaurant', 'restaurant_id');
    }

     public function getAssociateFeedbackRetingWithFeedback() {
        return $this->hasMany('App\Model\FeedbackRating', 'feedback_id');
    }

    public function getAssociateOrderForFeedback() {
        return $this->belongsTo('App\Model\Order', 'order_id');
    }

}



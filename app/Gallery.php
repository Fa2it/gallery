<?php

namespace App;


use App\User;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{

	protected $fillable =[ 'user_id','photo_id','title','thumbnailUrl',]; 

    /**
     * Scope a query to show most Favorite Photos .
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMostFavoritePhotos($query)
    {
        return $query->selectRaw('*, count(photo_id) as favorited')
        			->groupBy('photo_id')
        			->orderBy('favorited','DESC')
                    ->limit(5)
                     ->get();
    }

    /**
     * Scope a query to select user who favourite the most.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUsersWhoFavouritedTheMost($query)
    {
        $startOfWeek = now()->startOfWeek()->format('Y-m-d H:i');
        $endOfWeek =  now()->endOfWeek()->format('Y-m-d H:i');
        $r = $query->selectRaw('users.name as name , galleries.user_id, 
                         count(galleries.user_id) as most_favorited')
                    ->join( 'users', 'users.id', '=', 'galleries.user_id')
                     ->whereBetween('galleries.created_at', [$startOfWeek, $endOfWeek])
        			 ->groupBy('galleries.user_id')
        			->orderBy('most_favorited','DESC')
                    ->limit(5)
                     ->get();
        return $r;
    }

    /**
     * Scope a query to show stats base on during the week or the 
     * weekend
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStats($query)
    {
        if( now()->isWeekend() )
        	return ['User_Stats'=>self::UsersWhoFavouritedTheMost()];
        
        return ['Photo_Stats'=>self::MostFavoritePhotos()];

    }

    public static function validDate()
    {
    	return  request()->validate([
	        'user_id' => 'required',
	        'photo_id'=> 'required',
	        'title'   => 'required',
	        'thumbnailUrl' =>'required',
    	]);
    }
    /**
     * Scope a query to remove users Photo
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreateUserPhoto($query)
    {     
        return self::create( self::validDate() );
    }

    /**
     * Scope a query to remove users Photo
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     */
    public function scopeRemoveUserPhoto($query, int $id )
    {
		return request()->user()->gallaries()->where('photo_id', $id )->delete();

    } 

    /**
     * Creating a Relationship between photo and users
     *
     * @return  App\User
     */
    public function user(){

    	return $this->belongsTo(User::class);
    } 

    /**
     * Creating a Relationship between photo and users
     *
     * @return  App\User
     */
    public function scopeUserPhotos($query, int $start, int $limit){
        $start = ( $start < 0 ) ? 0 : $start;
        $limit = ( in_array($limit, range( 10, 100, 10 ) ) ) ? $limit : 20; 
    	$r = request()
    			->user()
				->gallaries()
				->whereBetween('photo_id', [ $start, $limit + $start ] )
                ->orderBy('photo_id','DESC')
				->get();
		return ['User_Photos'=>$r];
    }

 

}

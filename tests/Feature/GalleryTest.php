<?php

namespace Tests\Feature;

use App\User;
use App\Gallery;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GalleryTest extends TestCase
{
     use RefreshDatabase;
    /**
     * A Guess User can Access Main page
     *
     * @return void
     */
    public function testGuessUserCanAccessMainPage()
    {
        $this->get('/')
             ->assertStatus(200);
    }

    /**
     * A Guess User can Not Access Home page
     *
     * @return void
     */
    public function testGuessUserCanNotAccessHomePage()
    {

        $this->get('/home')
             ->assertRedirect('/login');
    }

    /**
     * A Register User can Access Home page
     *
     * @return void
     */
    public function testRegisterUserCanAccessHomePage()
    {
        // $this->withoutExceptionHandling(); 
        $user = factory(User::class)->create();
        $this->ActingAs( $user )->get('/home')
             ->assertStatus(200);
    }


    /**
     * A Guess Users can see a list of photos by favorite during the week
     *
     * @return void
     */
    public function testGuessUsersCanSeelistOfFavoritePhotosDuringtheWeek()
    {
        $this->withoutExceptionHandling();
        // factor to populate DB 
        $this->seedsUserGallery();

        $wkend = now()->startOfWeek();
        Carbon::setTestNow($wkend);

        $response = $this->get('/api/v1/photos/stats'); 

        $response->assertStatus(200);  
        $res = json_decode($response->content(), TRUE);

        // See that first record has 4 favourites 
        $this->assertEquals(4, $res['Photo_Stats'][0]['favorited']);

        // See that photo_id of the  first record is 4  
        $this->assertEquals(4, $res['Photo_Stats'][0]['photo_id']);
        // Good Idea always to reset to normal date
        Carbon::setTestNow(); 

    }

    /**
     * A Guess Users can see a list of Users Who favorite the Most during the week
     * Only on weekends
     *
     * @return void
     */
    public function testGuessUsersCanSeeWhoFavoriteTheMostDuringtheWeekOnlyOnWeekends()
    {
        $this->withoutExceptionHandling();
        // factor to populate DB 
        $this->seedsUserGallery();

        $wkend = now()->endOfWeek();
        Carbon::setTestNow($wkend);

        $response = $this->get('/api/v1/photos/stats'); 

        $response->assertStatus(200); 

        // See that User most favorited value is 4 
        $res = json_decode($response->content(), TRUE);

        $this->assertEquals(4, $res['User_Stats'][0]['most_favorited']);

        //dd( $res['User_Stats'][0] );
        // See that User 1 favorited the most ie 4 times
        $this->assertEquals(1, $res['User_Stats'][0]['user_id']);

        // Good Idea always to reset to normal date
        Carbon::setTestNow(); 

    }

   private function seedsUserGallery()
   {
     $user1 = factory(User::class)->create();
     $user2 = factory(User::class)->create();
     $user3 = factory(User::class)->create();
     $user4 = factory(User::class)->create();


    factory(Gallery::class)->create([
        'user_id'=> $user1->id,
        'photo_id'=> 1,
    ]);

    factory(Gallery::class)->create([
        'user_id'=> $user2->id,
        'photo_id'=> 1
    ]);

    factory(Gallery::class)->create([
        'user_id'=> $user3->id,
        'photo_id'=> 1
    ]);

    factory(Gallery::class)->create([
        'user_id'=> $user1->id,
        'photo_id'=> 2
    ]);

    factory(Gallery::class)->create([
        'user_id'=> $user2->id,
        'photo_id'=> 2
    ]);

    factory(Gallery::class)->create([
        'user_id'=> $user1->id,
        'photo_id'=> 3
    ]);

    factory(Gallery::class)->create([
        'user_id'=> $user1->id,
        'photo_id'=> 4
    ]);

    factory(Gallery::class)->create([
        'user_id'=> $user2->id,
        'photo_id'=> 4
    ]);

    factory(Gallery::class)->create([
        'user_id'=> $user3->id,
        'photo_id'=> 4
    ]);
    factory(Gallery::class)->create([
        'user_id'=> $user4->id,
        'photo_id'=> 4
    ]);
   }

    /**
     * A Register User can Favorite Photos via api
     * Assume that the External Api supplies photo on client side
     *
     * @return void
     */
    public function testRegisterUserCanFavoriteAndUnFavoritePhotos()
    {
        $this->withoutExceptionHandling(); 
        $user = factory(User::class)->create();
        $photo = factory(Gallery::class)->raw([
                    'user_id'=> $user->id,
                ]);

        // Favorite a Photo
        $this->ActingAs( $user, 'api' )->post('/api/v1/photos',$photo)
             ->assertStatus(200);

        // See that a record was saved in the database                 
        $this->assertDatabaseHas('galleries', $photo); 


        // UnFavorite a Photo
        $this->ActingAs( $user, 'api' )->delete('/api/v1/photos/'.$photo['photo_id'] )
             ->assertStatus(200);
             
        // See that the record was removed                
        $this->assertDatabaseMissing('galleries', $photo); 

    }

    /**
     * A Register User Can Not Favorite The Same Photo Twice
     * no dupicate record for a user for a unique photo_id
     *
     * @return void
     */
    public function testRegisterUserCanNotFavoriteSamePhotoTwice()
    {
        // $this->withoutExceptionHandling(); 
        $user = factory(User::class)->create();
        $photo = factory(Gallery::class)->raw([
                    'user_id'=> $user->id,
                ]);

        // Favorite a Photo
        $this->ActingAs( $user, 'api' )->post('/api/v1/photos',$photo)
             ->assertStatus(200);

        // See that a record was saved in the database                 
        $this->assertDatabaseHas('galleries', $photo); 

        // Favorite a Photo Again DB Constrain Exceptions
        $this->ActingAs( $user, 'api' )->post('/api/v1/photos',$photo)
             ->assertStatus(500);

        $this->assertEquals(1, Gallery::all()->count() );

    }
   
    /**
     * A Register User can See All His Favorite Photos via api
     * Assume that the External Api supplies photo on client side
     *
     * @return void
     */
    
    public function testRegisterUserCanSeeAllHerFavoritePhotos()
    {
        $this->withoutExceptionHandling(); 
        $this->seedsUserGallery();

        $user = User::first();
        $start = 0;
        $limits = 20;

        // User can see all her Favourite Photos
        $response = $this->ActingAs( $user, 'api' )->get('/api/v1/photos/'.$start.'/'.$limits);
        $response->assertStatus(200)
                 ->assertJsonCount(4, 'User_Photos');


    }



    /**
     * Test inverse Relations for User and Gallery
     * 
     * @return void
     */
    public function testGalleryPhotoHasUser()
    {
        $this->withoutExceptionHandling(); 

        $this->withoutExceptionHandling(); 
        $user = factory(User::class)->create();
        $photo = factory(Gallery::class)->create([
                    'user_id'=> $user->id,
                ]);

        $this->assertEquals( 1, $photo->user->id ) ;

    }


}

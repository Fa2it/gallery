<template>
    <div class="alert alert-primary">
                <div class="card" v-if="showUserStat">
                    <div class="card-header">Users Who Favorited the Most</div>
                    <div class="card-body">
                            <div class="table-responsive-sm">
                                <table class="table table-bordered">
                                  <thead>
                                    <tr>
                                      <th>Users Name</th>
                                       <th>Users #</th>
                                      <th>Favorited</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <tr v-for="item in Stats">
                                      <td> {{ item.name }}</td>
                                      <td> {{ item.user_id }}</td>
                                      <td> {{ item.most_favorited }}</td>
                                    </tr>
                                  </tbody>
                                </table>
                            </div>
                    </div>
                </div>

                <div class="card" v-if="showPhotoStat">
                    <div class="card-header">Most Favorited Five Photos</div>
                    <div class="card-body">
                            <div class="table-responsive-sm">
                                <table class="table table-bordered">
                                  <thead>
                                    <tr>
                                      <th>Photo</th>
                                      <th>Favorited</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <tr v-for="item in Stats">
                                      <td><img :src="item.thumbnailUrl" class="img-thumbnail" >
                                      <span class="ml-1 font-weight-bold">#{{item.photo_id}}</span>
                                      </td>
                                      <td> {{ item.favorited }}</td>
                                    </tr>
                                  </tbody>
                                </table>
                            </div>
                    </div>
                </div>
    </div>
</template>

<script>
    export default {
        data: function () {
          return {
            Stats: null,
            showUserStat: false,
            showPhotoStat: false,

          }
        },
        mounted() {
            axios.get('api/v1/photos/stats')
            .then((response) => {
                if( response.data.Photo_Stats !== undefined){
                    this.showPhotoStat = true;
                    this.Stats = response.data.Photo_Stats;
                }

                if( response.data.User_Stats !== undefined){
                    this.showUserStat = true;
                    this.Stats = response.data.User_Stats;
                }                
                  
            })
            .catch((error) => {
                // console.log(error);
            });
        }
    }
</script>

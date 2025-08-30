package co.edu.grocerystore.api;

import co.edu.grocerystore.model.Register;
import co.edu.grocerystore.model.ResponseCredentials;
import co.edu.grocerystore.model.ResponseUser;
import co.edu.grocerystore.model.User;
import retrofit2.Call;
import retrofit2.http.Body;
import retrofit2.http.DELETE;
import retrofit2.http.GET;
import retrofit2.http.Header;
import retrofit2.http.POST;
import retrofit2.http.PUT;
import retrofit2.http.Path;

public interface ServiceUsers {
    @POST("register")
    public Call<ResponseCredentials> accesRegister(@Body Register register);

    @GET("users/{id}")
    public Call<ResponseUser> getUser(@Path("id") String id, @Header("Authorization") String token);

    @PUT("users/{id}")
    public Call<ResponseCredentials> updateUser(@Path("id") String id, @Header("Authorization") String token, @Body User user);

    @DELETE("users/{id}")
    public Call<ResponseCredentials> deleteUser(@Path("id") String id, @Header("Authorization") String token);
}

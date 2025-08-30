package co.edu.grocerystore.api;

import co.edu.grocerystore.model.Car;
import co.edu.grocerystore.model.ResponseCar;
import co.edu.grocerystore.model.ResponseCredentials;
import co.edu.grocerystore.model.ResponseMessage;
import retrofit2.Call;
import retrofit2.http.Body;
import retrofit2.http.DELETE;
import retrofit2.http.GET;
import retrofit2.http.HTTP;
import retrofit2.http.Header;
import retrofit2.http.POST;
import retrofit2.http.PUT;
import retrofit2.http.Path;

public interface ServiceCars {
    @POST("cars")
    public Call<ResponseCredentials> addCar(@Body Car car, @Header("Authorization") String auth);

    @GET("cars/{id}")
    public Call<ResponseCar> getCars(@Path("id") String id, @Header("Authorization") String auth);

    @PUT("cars/{id}")
    public Call<ResponseCredentials> updateCar(@Path("id") String id, @Header("Authorization") String auth, @Body Car car);

    @DELETE("cars/{id}")
    public Call<ResponseMessage> deleteCar(@Path("id") String id, @Header("Authorization") String auth);
}

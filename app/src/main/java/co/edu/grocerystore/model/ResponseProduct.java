package co.edu.grocerystore.model;

import java.io.Serializable;
import java.util.ArrayList;

public class ResponseProduct implements Serializable {
    private ArrayList<Products> products;

    public ArrayList<Products> getProducts() {
        return products;
    }

    public void setProducts(ArrayList<Products> products) {
        this.products = products;
    }
}

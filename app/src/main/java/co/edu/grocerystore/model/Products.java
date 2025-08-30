package co.edu.grocerystore.model;

import java.io.Serializable;

public class Products implements Serializable {
    public int product_id;
    public int category_id;
    public String product_name;
    public int product_price;

    public int getProduct_id() {
        return product_id;
    }

    public void setProduct_id(int product_id) {
        this.product_id = product_id;
    }

    public int getCategory_id() {
        return category_id;
    }

    public void setCategory_id(int category_id) {
        this.category_id = category_id;
    }

    public String getProduct_name() {
        return product_name;
    }

    public void setProduct_name(String product_name) {
        this.product_name = product_name;
    }

    public int getProduct_price() {
        return product_price;
    }

    public void setProduct_price(int product_price) {
        this.product_price = product_price;
    }

    @Override
    public String toString() {
        return "Products{" +
                "product_id=" + product_id +
                ", category_id=" + category_id +
                ", product_name='" + product_name + '\'' +
                ", product_price=" + product_price +
                '}';
    }
}

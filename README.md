## Froots PHP Technical Challenge

### Setup

Create a new public Github repository and copy the contents of this file into the README.md, then please send us the link to the repository.

Please commit your work to the repository you created and remember to keep your commits clean and tidy.

### Prequisites:

For this challenge, you will need the following services running on your machine:

- PHP
- a http server (e.g. nginx, apache, etc...)
- a relational database (mysql, postgres, etc...)

You are free to set up those services in any way you like and can also use a pre-made dockerized development setup.

### Challenge:

1)

Create a new <https://api-platform.com/> Symfony project using composer

2)

Create a DB connection in Symfony using the .env file.

3)

Create these endpoints:

```
* orders: id, amount, created_at, updated_at
* products: id, name, price, created_at, updated_at
```

4)

An order can contain multiple products. When creating a new order, it should be possible to add products to the order. Write a custom endpoint via sub-resources that would make retrieving the data from the API possible in the following way:

```
order/123/products: Should return all the products in a specific order.
product/1/orders: Should return all the orders that have a specific product in them.
```

5)

Add a dynamic generated field for the custom endpoint called `product_url`

6)

Write tests for the above implementations.

Tips:

- You can use Data Faker to simplify seeding your DB.

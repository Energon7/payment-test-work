Start command: make install

curl -XPOST -H 'Accept: application/json' -H "Content-type: application/json" -d '{
"product": 1,
"taxNumber": "GR123456789",
"couponCode": "D15"
}' 'http://127.0.0.1/calculate-price'


curl -XPOST -H 'Accept: application/json' -H "Content-type: application/json" -d '{
"product": 1,
"taxNumber": "IT12345678900",
"couponCode": "D15",
"paymentProcessor": "paypal"
}' 'http://127.0.0.1/purchase'
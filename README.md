# Things need to be provided
 * .crt file
 * .key file that is used to generate the crs file which you sent to bank and got .crt file in return
 * .pem file built using the .key and .crt file

## Steps to generate the key file
 ``` openssl x509 -inform DER -outform PEM -in server.crt -out server.crt.pem ```
Then
  ``` cat server.crt server.key > server.includesprivatekey.pem ```

## After Completing the above steps then please get in contact with me. [Website](https://babinr.com.np) [facebook](https://facebook.com/ashwin.me.1) [linkedin](https://www.linkedin.com/in/babin-rana/)

## After you got API key for Test or live server. Go to WooCommerce > Settings > Payments > Babinr Payment Gateway > Manage or direcly by URL/wp-admin/admin.php?page=wc-settings&tab=checkout&section=babinr_gateway

* Place your API key Test or live and not forget to untick the Test mode if you are going to make the plugin in to live mode.

# URL Shortener

## API


URL                            | Method | Response Code | Response
------------------------------ | -------| --------------|-------------
shorten.batjaa.com/api/v1/urls | GET    | 200           | List of URLs. Supports Paging: `shorten.batjaa.com/api/v1/urls?offset=10&limit=10`
shorten.batjaa.com/api/v1/urls | POST   | 200           | Object with an error or if success the new created Url object

# Developing FPP Zettle

## FPP API
Check API methods at /api/help.php

We can get our config file from: 
GET /api/configfile/plugin.fpp-zettle.json



## Zettle API

### Create Event

Post request to url: https://pusher.izettle.com/organizations/self/subscriptions

Generate a UUID yourself. 

The destination will be put into the plugin config by the user, but is essentially the dataplicty address with our API endpoint on the end.

ie: https://unremarkable-cow-0123.dataplicity.io/api/plugin/fpp-zettle/event

Request body:
```json
{
  "uuid": "ef64c5e2-4e16-11e8-9c2d-fa7ae01bbebc",
  "transportName": "WEBHOOK",
  "eventNames": [
  "PurchaseCreated"
  ],
  "destination": "https://webhook.site/fb1a0755-a3ac-41ac-a996-7d1bf2106aae",
  "contactEmail": "fb1a0755-a3ac-41ac-a996-7d1bf2106aae@email.webhook.site"
}
```
Response - (Zettle says it uses signing key to verify request came from them - i read elsewhere it has no impact):

```json
{
    "uuid": "ef64c5e2-4e16-11e8-9c2d-fa7ae01bbebc",
    "transportName": "WEBHOOK",
    "eventNames": [
        "PurchaseCreated"
    ],
    "updated": "2022-04-25T15:24:32.185078Z",
    "destination": "https://webhook.site/fb1a0755-a3ac-41ac-a996-7d1bf2106aae",
    "contactEmail": "fb1a0755-a3ac-41ac-a996-7d1bf2106aae@email.webhook.site",
    "status": "ACTIVE",
    "signingKey": "wg1HcgcQSUY0kdTr5UllLtDWXRINszcVJS7942gmUuwoxasbUN0o6fyQtYDwuqa1"
}
```

### Testing the Zettle Pusher API recieves an Event.

I'm not sure atm what a Purchase message looks like. 

See https://developer.zettle.com/docs/api/pusher/user-guides/create-subscriptions

Can we extract any data?
Are we bothered?
Do we just trigger an effect?

Payload received:

```json
{
  "organizationUuid": "0530e628-c1b1-11ec-ab70-336b73208bd4",
  "messageUuid": "ce563614-c4ab-11ec-9997-75dbd69f005f",
  "eventName": "TestMessage",
  "messageId": "ce563646-c4ab-11ec-bb29-45eefc4d0824",
  "payload": "{\"data\":\"payload\"}",
  "timestamp": "2022-04-25T15:24:32.185098Z"
}
```

### Config file lives in ~/media/config/

```shell
cat ~/media/config/plugin.fpp-zettle.json 
```
Config file:
```json
{
   "client_id": "36bf2d88-c1b6-11ec-b2c6-bb62f5c687b6",
   "client_secret": "somemassiveid",
   "organizationUuid": "0530e628-c1b1-11ec-ab70-336b73208bd4",
   "subscriptions": [
      {
         "contactEmail": "a0c47472-a6d6-4d1d-938b-a3979d46e151@email.webhook.site",
         "destination": "https://webhook.site/a0c47472-a6d6-4d1d-938b-a3979d46e151",
         "eventNames": [
            "PurchaseCreated"
         ],
         "signingKey": "TEJPW5mc0y6uzKhnC0g9w1mWuRUV7Y4p6bG27EYTKHMdU8pmxe8oOO9qgUX5m6w4",
         "status": "ACTIVE",
         "transportName": "WEBHOOK",
         "updated": "2022-04-25T23:44:36.190Z",
         "uuid": "ef64c5e2-4e16-11e8-9c2d-aa6ae01bbebc"
      }
   ]
}
```


### TODO

1. Register Domain either dataplicity or noip
   1. https://unrenowned-sheep-0265.dataplicity.io
2. Create subscription at zettle pointing at https://unrenowned-sheep-0265.dataplicity.io


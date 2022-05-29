# Announce Zettle!

[![Pull Requests Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen.svg?style=flat)](http://makeapullrequest.com)
![FPP Zettle Logo](https://shields.io/badge/fpp-AnnounceZettle-brightgreen)

Get yourself an iZettle device, take a donation & trigger an effect on your light show.

## Getting Started

You'll need an iZettle device to use this plugin.

Either [register](https://register.zettle.com/gb) or [login](https://login.zettle.com/) with Zettle.

![Zettle API Integrations](./img/zettle-api-key.png)

- Click 'Integrations' (Bottom Left)
- Click 'API Keys'
- Click 'Create API Key'
- Type a name for your API Key.
- Select 'READ:USERINFO and READ:PURCHASE'
- Click 'Create Key'

You will now be presented with 2 attributes you need to copy & keep safe. They won't be retrievable again so make sure you capture them:
- client_id
- API Key

## FPP

Navigate to your FPP instance. 

Click 'Content Setup' > 'Plugin Manager'

Install the Announce Zettle plugin.

Once installed, navigate to 'Content Setup' > 'Zettle - Setup'.

Add your `Client ID` and `Secret` to the page & click 'Save'

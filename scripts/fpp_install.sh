#!/bin/bash

# fpp-zettle install script

echo "Installing Announce Zettle Plugin for FPP...."

echo "Writing config file...."

echo "{\"client_id\": \"\",\"client_secret\": \"\",\"organizationUuid\": \"\",\"subscriptions\": \"\"}" > /home/fpp/media/config/plugin.fpp-zettle.json

echo "You need a secure https endpoint on your pi to use this plugin. Dataplicity is the easiest way to achieve that."

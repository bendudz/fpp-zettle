#!/bin/bash

# fpp-zettle install script

function writeDefaultJsonToConfig()
{
defalt_json=$(cat <<EOF
{
   "client_id": "",
   "client_secret": "",
   "organizationUuid": "",
   "subscriptions": [],
   "effect": "",
   "command": ""
}
EOF
)
echo "$defalt_json" > /home/fpp/media/config/plugin.fpp-zettle.json
}

echo "Installing Announce Zettle Plugin for FPP...."

echo "Writing config file...."

file=/home/fpp/media/config/plugin.fpp-zettle.json

if [ -s "$file" ]
then
   echo " Config file exists and is not empty... continuing "
else
   echo " Config file does not exist, or is empty "
   touch $file
   writeDefaultJsonToConfig()
   sudo chown fpp /home/fpp/media/config/plugin.fpp-zettle.json
fi

echo "You need a secure https endpoint on your pi to use this plugin. Dataplicity is the easiest way to achieve that. Check out the readme or the plugin help text for more information."

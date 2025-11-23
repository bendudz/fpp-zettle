#!/bin/bash

# fpp-zettle install script
echo "Installing Announce Zettle Plugin for FPP...."

echo "Writing config file...."

file=/home/fpp/media/config/plugin.fpp-zettle.json

defalt_json=$(cat <<EOF
{
   "client_id": "",
   "client_secret": "",
	"organizationUuid": "",
   "subscriptions": [],
	 "effect_activate": "no",
	"command": "",
	"publish": ["activate": "yes"],
}
EOF
)

if [ -s "$file" ]
then
	echo " Config file exists and is not empty... continuing "
else
	echo " Config file does not exist, or is empty "
   	touch $file
	echo "$defalt_json" > /home/fpp/media/config/plugin.fpp-zettle.json
	sudo chown fpp /home/fpp/media/config/plugin.fpp-zettle.json
fi

echo "You need a secure https endpoint on your pi to use this plugin. Dataplicity is the easiest way to achieve that. Check out the readme or the plugin help text for more information."

echo "Please restart fppd for new FPP Commands to be visible."
. /opt/fpp/scripts/common
setSetting restartFlag 1
#!/bin/bash

PLUGIN_DIR="$(dirname "$0")"

# Log to /tmp first (always writable), then also try the media logs dir
LOGFILE="/tmp/fppZettle_install.log"
MEDIA_LOG="/home/fpp/media/logs/fppZettle_install.log"

log() {
    local msg="[$(date '+%Y-%m-%d %H:%M:%S')] $*"
    echo "$msg" | tee -a "$LOGFILE"
    echo "$msg" >> "$MEDIA_LOG" 2>/dev/null || true
}

log "=== Announce Zettle install started (user=$(whoami), uid=$(id -u)) ==="

# ── Create media directories ─────────────────────────────────────
# Do this FIRST so the media log path is available.
mkdir -p /home/fpp/media/logs
mkdir -p /home/fpp/media/config

# Now that the dir exists, copy /tmp log into media log
cat "$LOGFILE" >> "$MEDIA_LOG" 2>/dev/null || true

# ── Make scripts executable ──────────────────────────────────────
log "Setting script permissions..."
chmod +x "${PLUGIN_DIR}/scripts/"*.py 2>/dev/null || true
chmod +x "${PLUGIN_DIR}/scripts/"*.sh 2>/dev/null || true
chmod +x "${PLUGIN_DIR}/commands/"*.sh 2>/dev/null || true
chmod +x "${PLUGIN_DIR}/fpp_start.sh"  2>/dev/null || true
chmod +x "${PLUGIN_DIR}/fpp_stop.sh"   2>/dev/null || true

# ── Write default config if none exists ─────────────────────────
CONFIG="/home/fpp/media/config/plugin.fpp-zettle.json"
if [[ ! -f "$CONFIG" ]]; then
log "Writing default config to $CONFIG"
    cp "${PLUGIN_DIR}/config/fpp-zettle.json.example" "$CONFIG" 2>/dev/null || \
    cat > "$CONFIG" <<'JSONEOF'
{
	"client_id": "",
	"client_secret": "",
	"organizationUuid": "",
	"subscriptions": [],
	"effect_activate": "no",
	"command": "",
	"multisyncCommand": false,
    "multisyncHosts": "",
	"publish": {"activate": "yes"},
	"pushover": {
    	"activate": "no",
    	"app_token": "",
    	"user_key": "",
    	"message": ""
  	},
  	"other": {
    	"currency": "GBP"
  	}
}
JSONEOF
fi

shell_exec("sudo chown -R fpp:fpp /home/fpp/media/config/plugin.fpp-zettle.json");

TRANSACTIONS="/home/pi/fpp/config/plugin.fpp-zettle-transactions.json"
log "Writing default transactions to $CONFIG"
    cp "${PLUGIN_DIR}/config/fpp-zettle-transactions.json.example" "$CONFIG" 2>/dev/null || \
    cat > "$CONFIG" <<'JSONEOF'
[]
JSONEOF
fi

echo "You need a secure https endpoint on your pi to use this plugin. Dataplicity is the easiest way to achieve that. Check out the readme or the plugin help text for more information."

echo "Please restart fppd for new FPP Commands to be visible."
. /opt/fpp/scripts/common
setSetting restartFlag 1

#!/bin/bash
# fpp_install.sh — Announce Zettle plugin installer
# Called by FPP when the plugin is installed or updated.

PLUGIN_DIR="$(dirname "$0")"

# Resolve FPP's logs directory the documented way (supports a relocated
# media directory) rather than hard-coding /home/fpp/media/logs, and use
# the single FPP-conformant log file (plugin-<repoName>.log) for both this
# install script and the daemon, per the plugin guidelines' logging rules.
: "${FPPDIR:=/opt/fpp}"
. "${FPPDIR}/scripts/common" 2>/dev/null || true
LOGDIR="$(getSetting logDirectory 2>/dev/null)"
LOGDIR="${LOGDIR:-/home/fpp/media/logs}"
LOGFILE="${LOGDIR}/plugin-fpp-zettle.log"

# Log to /tmp first (always writable), then also try the media logs dir
LOGFILE="/tmp/fppZettle_install.log"
MEDIA_LOG="/home/fpp/media/logs/fppZettle_install.log"

log() {
    local msg="[$(date '+%Y-%m-%d %H:%M:%S')] $*"
    mkdir -p "$LOGDIR" 2>/dev/null || true
    echo "$msg" >> "$LOGFILE" 2>/dev/null || echo "$msg"
}

log "=== Announce Zettle install started (user=$(whoami), uid=$(id -u)) ==="

# ── Create media directories ─────────────────────────────────────
# (log() already mkdir -p's $LOGDIR on every call)
# Do this FIRST so the media log path is available.
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
setSetting restartFlag 1 2>/dev/null || true

log "=== Announce Zettle install complete ==="
exit 0

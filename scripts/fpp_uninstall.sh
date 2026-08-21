#!/bin/bash
# fpp_uninstall.sh — Announce Zettle plugin uninstaller
# Called by FPP when the plugin is removed. Mirrors fpp_install.sh's

log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $*"
}

log "=== Announce Zettle uninstall started ==="
log "There is nothing to remove"
log "=== Announce Zettle uninstall complete. Config in place. ==="

source ${FPPDIR}/scripts/common; setSetting restartFlag 1
exit 0

#!/bin/bash
# fpp_uninstall.sh — Announce Zettle plugin uninstaller
# Called by FPP when the plugin is removed. Mirrors fpp_install.sh's

log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $*"
}

log "=== Announce Zettle uninstall started ==="

log "=== Announce Zettle uninstall complete. Config in place. ==="
exit 0

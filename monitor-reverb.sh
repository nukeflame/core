#!/bin/bash
# Laravel Reverb Production Health Monitor
# Author: pk305
# Description: Checks Supervisor, Nginx, Reverb port, logs, and memory usage

set -euo pipefail

SCRIPT_NAME=$(basename "$0")

# Ensure script is executable
if [ ! -x "$0" ]; then
    echo "Making $SCRIPT_NAME executable..."
    chmod +x "$0"
    echo "Restarting $SCRIPT_NAME..."
    exec "./$SCRIPT_NAME"
fi

echo "=== Reverb Production Status Report ==="
echo "Generated at: $(date '+%Y-%m-%d %H:%M:%S')"
echo ""

# 1. Supervisor Status
echo "1. Supervisor Status:"
sudo supervisorctl status reinsurance-reverb || echo "⚠️ Unable to fetch Supervisor status."
echo ""

# 2. Port 8080 Status
echo "2. Port 8080 Status:"
sudo ss -tulpn | grep 8080 || echo "⚠️ Port 8080 not listening."
echo ""

# 3. Recent Reverb Logs
echo "3. Recent Reverb Logs:"
sudo supervisorctl tail -200 reinsurance-reverb 2>/dev/null || echo "⚠️ No logs found."
echo ""

# 4. Nginx Status
echo "4. Nginx Status:"
sudo systemctl status nginx | grep Active || echo "⚠️ Nginx inactive or not installed."
echo ""

# 5. WebSocket Endpoint Test
echo "5. WebSocket Endpoint Test:"
curl -I https://reinsurance.acentriagroup.com/app 2>&1 | head -5 || echo "⚠️ Endpoint unreachable."
echo ""

# 6. Reverb Process Memory
echo "6. Reverb Process Memory:"
ps aux | grep "reverb:start" | grep -v grep || echo "⚠️ Reverb process not running."
echo ""

# Optional: Simple summary
if sudo supervisorctl status reinsurance-reverb | grep -q RUNNING; then
    echo "✅ Reverb service is RUNNING."
else
    echo "❌ Reverb service is NOT running."
fi

echo ""
echo "=== End of Report ==="

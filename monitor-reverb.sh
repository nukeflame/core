#!/bin/bash

echo "=== Reverb Production Status ==="
echo ""

# Check Supervisor status
echo "1. Supervisor Status:"
sudo supervisorctl status reinsurance-reverb
echo ""

# Check if port is listening
echo "2. Port 8080 Status:"
sudo ss -tulpn | grep 8080
echo ""

# Check recent logs (last 20 lines)
echo "3. Recent Reverb Logs:"
sudo supervisorctl tail reinsurance-reverb | tail -20
echo ""

# Check Nginx status
echo "4. Nginx Status:"
sudo systemctl status nginx | grep Active
echo ""

# Test WebSocket endpoint
echo "5. WebSocket Endpoint Test:"
curl -I https://reinsurance.acentriagroup.com/app 2>&1 | head -5
echo ""

# Check memory usage
echo "6. Reverb Process Memory:"
ps aux | grep "reverb:start" | grep -v grep
echo ""

echo "=== End of Report ==="

# chmod +x monitor-reverb.sh
# ./monitor-reverb.sh

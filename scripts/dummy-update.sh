#!/bin/bash
# Dummy update script used only for webhook tests
LOG_FILE="$(pwd)/webhook.test.log"
echo "$(date '+%Y-%m-%d %H:%M:%S') - DUMMY UPDATE START ($1)" >> "$LOG_FILE"
sleep 1
echo "$(date '+%Y-%m-%d %H:%M:%S') - DUMMY UPDATE END ($1)" >> "$LOG_FILE"
exit 0

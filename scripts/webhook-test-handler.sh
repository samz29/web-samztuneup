#!/bin/bash

# Local test copy of webhook-handler.sh
# Writes to ./webhook.test.log and calls ./scripts/dummy-update.sh

SECRET="test_secret"
LOG_FILE="$(pwd)/webhook.test.log"
UPDATE_SCRIPT="$(pwd)/scripts/dummy-update.sh"

log() {
    echo "$(date '+%Y-%m-%d %H:%M:%S') - $1" >> "$LOG_FILE"
}

BODY=$(cat)
GITHUB_SIGNATURE=$(echo -n "$BODY" | openssl dgst -sha256 -hmac "$SECRET" -binary | xxd -p | tr -d '\n')
EXPECTED_SIGNATURE="sha256=$GITHUB_SIGNATURE"
RECEIVED_SIGNATURE=$1

log "[TEST] Webhook received"
log "[TEST] Expected signature: $EXPECTED_SIGNATURE"
log "[TEST] Received signature: $RECEIVED_SIGNATURE"

EVENT_TYPE=$(echo "$BODY" | grep -o '"ref":"[^"]*"' | cut -d'"' -f4)
BRANCH=$(echo "$EVENT_TYPE" | sed 's|refs/heads/||')

log "[TEST] Event: $EVENT_TYPE"
log "[TEST] Branch: $BRANCH"

if [ "$BRANCH" = "main" ]; then
    log "[TEST] Starting deployment for branch: $BRANCH"
    nohup bash "$UPDATE_SCRIPT" "$BRANCH" >> "$LOG_FILE" 2>&1 &
    echo "Deployment started"
    log "[TEST] Deployment process started in background"
else
    log "[TEST] Ignoring push to branch: $BRANCH"
    echo "Ignored - not main branch"
fi

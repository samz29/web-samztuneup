#!/bin/bash

# GitHub Webhook Handler for Auto Deployment
# Place this in your web-accessible directory (e.g., /var/www/webhooks/)

# Configuration
SECRET="your_webhook_secret_here"  # Set this in GitHub webhook settings
LOG_FILE="/home/samztekn/webhook.log"
UPDATE_SCRIPT="/home/samztekn/samztuneup/update.sh"

# Function to log messages
log() {
    echo "$(date '+%Y-%m-%d %H:%M:%S') - $1" >> "$LOG_FILE"
}

# Get the request body
BODY=$(cat)

# Get GitHub signature from headers
GITHUB_SIGNATURE=$(echo "$BODY" | openssl dgst -sha256 -hmac "$SECRET" -binary | xxd -p | tr -d '\n')

# Expected signature from GitHub
EXPECTED_SIGNATURE="sha256=$GITHUB_SIGNATURE"

# Get signature from request (you need to set this in your web server)
# This depends on how you set up the webhook endpoint
RECEIVED_SIGNATURE=$1  # Pass as argument or get from headers

log "Webhook received"
log "Expected signature: $EXPECTED_SIGNATURE"
log "Received signature: $RECEIVED_SIGNATURE"

# Verify signature (uncomment when signature verification is set up)
# if [ "$EXPECTED_SIGNATURE" != "$RECEIVED_SIGNATURE" ]; then
#     log "Signature verification failed"
#     echo "Unauthorized"
#     exit 1
# fi

# Parse JSON payload (simple parsing)
EVENT_TYPE=$(echo "$BODY" | grep -o '"ref":"[^"]*"' | cut -d'"' -f4)
BRANCH=$(echo "$EVENT_TYPE" | sed 's|refs/heads/||')

log "Event: $EVENT_TYPE"
log "Branch: $BRANCH"

# Only deploy on push to main branch
if [ "$BRANCH" = "main" ]; then
    log "Starting deployment for branch: $BRANCH"

    # Run update script in background
    nohup bash "$UPDATE_SCRIPT" "$BRANCH" >> "$LOG_FILE" 2>&1 &

    echo "Deployment started"
    log "Deployment process started in background"
else
    log "Ignoring push to branch: $BRANCH"
    echo "Ignored - not main branch"
fi
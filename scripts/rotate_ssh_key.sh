#!/bin/bash
set -euo pipefail

# rotate_ssh_key.sh
# - Backup ~/.ssh/authorized_keys
# - Generate a new ed25519 key (safe default)
# - Append new public key to authorized_keys
# - Remove a specific compromised public key from authorized_keys
# - Print the new public key for copying to GitHub / other servers

KEY_TO_REMOVE='ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQDXTAgO/HP21l//RZataTmZSZ2TyhZQ80ogeNKR7OiheYzdK3ZegKZVJzpxNfx7ZG9ElmtxJj7ESTwxIEjSII0ssavnN5VtC66aqveJpRoVfb48DqjYNh3Exxd6zhbTasbLI4S0C1xEBA1nGjcOHG9SLVht66whwWp8JmAJuvH3WqXD6EqubOeVNLmtpr/8wZAgCE8Y4ZkJ5lY0IjQHOydr+mPhiXLwx07jAsqk/CD8GRRan4sDC7AWmh8k3E136VpJD03v8BvhvUT+T6WzYQmG0J2h1mOSNGMeCeaouVP+pNtVCLNDewCGmoohZ2PZ5cda0fGaH2GGL5CZ6e6gwRAz'

AUTHORIZED_KEYS="$HOME/.ssh/authorized_keys"
BACKUP_DIR="$HOME/.ssh/backups/authorized_keys-$(date +%F_%H%M%S)"
DEFAULT_NEW_KEY="$HOME/.ssh/id_ed25519_prod"

echo "== SSH key rotation helper =="
echo

# Ensure .ssh exists
mkdir -p "$HOME/.ssh"
chmod 700 "$HOME/.ssh"

# Backup authorized_keys (if exists)
if [ -f "$AUTHORIZED_KEYS" ]; then
  mkdir -p "$BACKUP_DIR"
  cp -v "$AUTHORIZED_KEYS" "$BACKUP_DIR/authorized_keys.bak"
  echo "Backup created: $BACKUP_DIR/authorized_keys.bak"
else
  echo "No existing authorized_keys found — creating new file."
  touch "$AUTHORIZED_KEYS"
  chmod 600 "$AUTHORIZED_KEYS"
fi

# Ask for new key filename (default)
read -p "New key file path (default: $DEFAULT_NEW_KEY): " NEW_KEY_PATH
if [ -z "$NEW_KEY_PATH" ]; then
  NEW_KEY_PATH="$DEFAULT_NEW_KEY"
fi

# If file exists, suffix with timestamp to avoid accidental overwrite
if [ -f "$NEW_KEY_PATH" ] || [ -f "${NEW_KEY_PATH}.pub" ]; then
  TS=$(date +%s)
  NEW_KEY_PATH="${NEW_KEY_PATH}_${TS}"
  echo "Key file exists — using: $NEW_KEY_PATH"
fi

# Ask for optional passphrase
read -s -p "Enter passphrase for new key (leave empty for none): " PASS
echo

# Generate new ed25519 key
echo "Generating new key: $NEW_KEY_PATH"
ssh-keygen -t ed25519 -C "deploy@$(hostname)-$(date +%F)" -f "$NEW_KEY_PATH" -N "$PASS" >/dev/null
chmod 600 "$NEW_KEY_PATH"
chmod 644 "${NEW_KEY_PATH}.pub"

# Append new public key to authorized_keys if not already present
if grep -qF "$(cat ${NEW_KEY_PATH}.pub)" "$AUTHORIZED_KEYS"; then
  echo "Public key already present in $AUTHORIZED_KEYS"
else
  cat "${NEW_KEY_PATH}.pub" >> "$AUTHORIZED_KEYS"
  echo "Appended new public key to $AUTHORIZED_KEYS"
fi

# Remove compromised key (exact match)
if grep -qF "$KEY_TO_REMOVE" "$AUTHORIZED_KEYS"; then
  grep -vF "$KEY_TO_REMOVE" "$AUTHORIZED_KEYS" > "${AUTHORIZED_KEYS}.tmp"
  mv "${AUTHORIZED_KEYS}.tmp" "$AUTHORIZED_KEYS"
  chmod 600 "$AUTHORIZED_KEYS"
  echo "Compromised key removed from $AUTHORIZED_KEYS"
else
  echo "Compromised key not found in $AUTHORIZED_KEYS (may have been removed already)"
fi

# Final permissions check
chmod 700 "$HOME/.ssh"
chmod 600 "$AUTHORIZED_KEYS"

# Print new public key for copy/paste
echo
echo "== NEW PUBLIC KEY (copy this to GitHub / other servers) =="
echo
cat "${NEW_KEY_PATH}.pub"
echo

echo "== Done. Backup is at: $BACKUP_DIR =="
echo "Test login with: ssh -i ${NEW_KEY_PATH} user@server-ip"
echo
exit 0

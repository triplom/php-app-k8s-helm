#!/usr/bin/env bash
# Deploy the php-app Helm chart.
# Credentials must be supplied via a separate values file — never commit secrets.
#
# Usage:
#   cp helm-values-secret.yaml.example helm-values-secret.yaml
#   # Edit helm-values-secret.yaml with real credentials
#   bash helm.sh
#
# IMPORTANT: helm-values-secret.yaml is listed in .gitignore and must never be committed.

set -euo pipefail

helm upgrade --install phpfpm . \
  --values helm-values-secret.yaml \
  --wait \
  --timeout 5m

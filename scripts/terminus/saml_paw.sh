#!/bin/bash

# Check if HASHED_SAML_ADMIN_PAW is set
if [ -z "$HASHED_SAML_ADMIN_PAW" ]; then
  echo "Error: HASHED_SAML_ADMIN_PAW is not set."
  echo "Please export the HASHED_SAML_ADMIN_PAW environment variable before running $0."
  exit 1
fi

# Define the array of domains
domains=(
    "uls-upstream-test"
    "main-cul"
    "digitalscholarship-library-cornell-edu"
    "uls-africana-library-cornell-edu"
    "uls-annex-library-cornell-edu"
    "uls-asia-library-cornell-edu"
    "uls-engineering-library-cornell-edu"
    "uls-finearts-library-cornell-edu"
    "uls-hotel-library-cornell-edu"
    "uls-ilr-library-cornell-edu"
    "uls-law-library-cornell-edu"
    "uls-library-cornell-edu"
    "uls-management-library-cornell-edu"
    "uls-mann-library-cornell-edu"
    "uls-mathematics-library-cornell-edu"
    "uls-music-library-cornell-edu"
    "uls-olinuris-library-cornell-edu"
    "uls-psl-library-cornell-edu"
    "uls-rare-library-cornell-edu"
    "uls-techls-library-cornell-edu"
    "uls-vet-library-cornell-edu"
)

branches=(
    "getupstream"
    "dev"
    "test"
    "live"
)

# Loop over each element in the array
for domain in "${domains[@]}"; do
    # Perform an action with each domain
    echo "***"
    echo "***"
    echo "Processing domain: $domain"
    # Add your custom action here, e.g., ping the domain
    for branch in "${branches[@]}"; do
        TERMINUS_SITE="$domain.$branch"
        echo "***"
        echo "Processing branch: $TERMINUS_SITE"
        terminus env:view --print "$TERMINUS_SITE" || {
            echo "Error: $TERMINUS_SITE not found."
            continue
        }
        echo "Setting SAML_ADMIN_PAW for $TERMINUS_SITE"
        terminus secrets:set "$TERMINUS_SITE" SAML_ADMIN_PAW "$HASHED_SAML_ADMIN_PAW"
        terminus secrets:show "$TERMINUS_SITE" SAML_ADMIN_PAW
        terminus secrets:show "$TERMINUS_SITE" SAML_SALT || {
            echo "Generating SAML_SALT for $TERMINUS_SITE"
            SALT=`LC_ALL=C tr -c -d '0123456789abcdefghijklmnopqrstuvwxyz' </dev/urandom | dd bs=32 count=1 2>/dev/null;echo`
            terminus secrets:set "$TERMINUS_SITE" SAML_SALT "$SALT"
            terminus secrets:show "$TERMINUS_SITE" SAML_SALT
        }
    done
done
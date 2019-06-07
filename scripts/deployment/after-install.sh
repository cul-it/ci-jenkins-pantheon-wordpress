#!/bin/bash
# after-install.sh - clean up after wordpress has been installed

GITROOT=`git rev-parse --show-toplevel`
echo "Removing vulnerable files un-needed after Wordpress install"
echo "..."
INSTALL="$GITROOT/web/wp/wp-admin/install.php"
[ -e $INSTALL ] && rm -i $INSTALL
echo "..."
UPGRADE="$GITROOT/web/wp/wp-admin/upgrade.php"
[ -e $UPGRADE ] && rm -i $UPGRADE
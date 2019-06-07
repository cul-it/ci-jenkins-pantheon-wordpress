#!/bin/bash
# before-install.sh - prepare fresh upstream repo for Pantheon

# First we define the function
function ConfirmOrExit() {
while true
do
echo -n "Please confirm (y or n) :"
read CONFIRM
case $CONFIRM in
y|Y|YES|yes|Yes) break ;;
n|N|no|NO|No)
echo Aborting - you entered $CONFIRM
exit
;;
*) echo Please enter only y or n
esac
done
echo You entered $CONFIRM. Continuing ...
}

echo "Prepare upstream for Pantheon"
git status
echo "You should already be on the feature branch, right?"
ConfirmOrExit

GITROOT=`git rev-parse --show-toplevel`

cd $GITROOT
pwd

echo "Modifying .gitignore to include artifacts in the Pantheon git repo"
PATCH="$GITROOT/patches/gitignore-artifacts-patch.diff"
patch -p1 < $PATCH
git commit .gitignore -m 'patched .gitignore to include artifacts for Pantheon'

echo "Creating the artifacts with composer"
composer install
git add -A && git commit -m 'ran composer install'

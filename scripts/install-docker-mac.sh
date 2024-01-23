#!/bin/bash
printf '\033[36m Устанавливаю Brew\033[0m\n'

which -s brew
if [[ $? != 0 ]]; then
     # Install Homebrew
     curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh
else
     brew update
fi

printf '\033[36m Устанавливаю свежую версию Docker\033[0m\n'
brew install --cask docker

printf '\033[36m Запускаю Docker\033[0m\n'
#Open Docker, only if is not running
if (! docker stats --no-stream ); then
  # On macOS this would be the terminal command to launch Docker
  open -a Docker
 #Wait until Docker daemon is running and has completed initialisation
while (! docker stats --no-stream ); do
  # Docker takes a few seconds to initialize
  sleep 2
done
fi
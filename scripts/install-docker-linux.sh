#!/bin/bash

GREEN='\033[0;32m'
RED='\033[0;31m'
RESET='\033[0m'

function add_to_docker_group_and_enable() {
  echo -e "${GREEN}Start and enable the Docker service${RESET}"
  sudo systemctl start docker
  sudo systemctl enable docker

  echo -e "${GREEN}Add the current user to the docker group${RESET}"
  sudo usermod -aG docker "$USER"
  newgrp docker
}

# install docker on Debian or Ubuntu
function install_debian_ubuntu() {
  if check_docker_installed; then
    if check_docker_compose_installed; then
      echo -e "${GREEN}Docker is already installed.${RESET}"
    else
      sudo apt update
      sudo apt -y install docker-compose-plugin docker-compose
    fi

  else
    # Add Docker's official GPG key:
    sudo apt update
    sudo apt -y install ca-certificates curl gnupg
    sudo install -m 0755 -d /etc/apt/keyrings
    curl -fsSL https://download.docker.com/linux/$DISTRO/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg
    sudo chmod a+r /etc/apt/keyrings/docker.gpg
    # Add the repository to Apt sources:
    # If you use an Ubuntu derivative distro, such as Linux Mint, you may need to use UBUNTU_CODENAME instead of VERSION_CODENAME.
    echo \
      "deb [arch="$(dpkg --print-architecture)" signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/$DISTRO \
            "$(. /etc/os-release && echo "$VERSION_CODENAME")" stable" |
      sudo tee /etc/apt/sources.list.d/docker.list >/dev/null
    sudo apt update

    # Install Docker
    sudo apt -y install docker-ce docker-ce-cli containerd.io docker-compose-plugin docker-compose
    add_to_docker_group_and_enable
  fi
}

# install docker on Manjaro or Arch
function install_arch_manjaro() {
  if check_docker_installed; then
    if check_docker_compose_installed; then
      echo "Docker is already installed."
    else
      sudo pacman -Syu --noconfirm
      sudo pacman -S --noconfirm docker docker-buildx containerd docker-compose
      add_to_docker_group_and_enable
    fi

  else
    sudo pacman -Syu --noconfirm
    sudo pacman -S --noconfirm docker docker-buildx containerd docker-compose
    add_to_docker_group_and_enable
  fi
}

# Function to check if Docker is installed
function check_docker_installed() {
  if docker --version >/dev/null 2>&1; then
    return 0 # Docker is installed
  else
    return 1 # Docker is not installed
  fi
}

# Function to check if Docker Compose is installed
function check_docker_compose_installed() {
  if docker compose version >/dev/null 2>&1; then
    return 0 # Docker Compose is installed
  else
    return 1 # Docker Compose is not installed
  fi
}


# Detect the Linux distribution
if [ -f /etc/os-release ]; then
  . /etc/os-release
  DISTRO=$ID
else
  echo -e "${RED}Unable to detect the Linux distribution. Script may not work for this distribution.${RESET}"
  exit 1
fi

# Install Docker based on the distribution
echo -e "${GREEN}Install docker for $DISTRO$RESET"
case $DISTRO in
"ubuntu" | "debian")
  install_debian_ubuntu
  ;;

"manjaro" | "arch")
  install_arch_manjaro
  ;;

*)
  echo -e "${RED}Unsupported Linux distribution: $DISTRO. Script may not work for this distribution.${RESET}"
  exit 1
  ;;
esac

-include .env.tunnel

SSH_TUNNEL_SOCKET := $(CURDIR)/.ssh-tunnel.sock

.PHONY: up tunnel tunnel-stop

up: tunnel
	docker compose up -d

tunnel:
	@test -n "$(SSH_TUNNEL_TARGET)" -a -n "$(SSH_TUNNEL_BIND)" -a -n "$(SSH_TUNNEL_LOCAL_PORT)" -a -n "$(SSH_TUNNEL_REMOTE_HOST)" -a -n "$(SSH_TUNNEL_REMOTE_PORT)" || { echo 'Complete .env.tunnel using .env.tunnel.example' >&2; exit 1; }
	@if ssh -S "$(SSH_TUNNEL_SOCKET)" -O check "$(SSH_TUNNEL_TARGET)" >/dev/null 2>&1; then \
		echo 'SSH tunnel is already running'; \
	else \
		rm -f "$(SSH_TUNNEL_SOCKET)"; \
		ssh -M -S "$(SSH_TUNNEL_SOCKET)" -f -N -o ExitOnForwardFailure=yes \
			-L "$(SSH_TUNNEL_BIND):$(SSH_TUNNEL_LOCAL_PORT):$(SSH_TUNNEL_REMOTE_HOST):$(SSH_TUNNEL_REMOTE_PORT)" \
			"$(SSH_TUNNEL_TARGET)"; \
	fi

tunnel-stop:
	@test -n "$(SSH_TUNNEL_TARGET)" || { echo 'Set SSH_TUNNEL_TARGET in .env.tunnel (see .env.tunnel.example)' >&2; exit 1; }
	ssh -S "$(SSH_TUNNEL_SOCKET)" -O exit "$(SSH_TUNNEL_TARGET)"

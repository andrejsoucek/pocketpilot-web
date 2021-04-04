FROM node:8.12.0-alpine
ARG USER_ID
ARG GROUP_ID

WORKDIR /pocketpilot

COPY /init.sh /

RUN if [ -n "$USER_ID" ] && [ "${USER_ID}" != 0 ]; then \
        if getent group "$GROUP_ID"; then \
            echo "developer:x:$GROUP_ID:" >> /etc/group; \
        else \
            addgroup -g "${GROUP_ID}" developer || exit 1; \
        fi; \
        if getent passwd "$USER_ID"; then \
            echo "developer:x:$USER_ID:$GROUP_ID:Linux User,,,:/:/bin/sh" >> /etc/passwd; \
        else \
            adduser -u "${USER_ID}" -G developer -D developer || exit 1; \
        fi; \
        chown developer:developer /pocketpilot; \
    fi

USER developer

CMD ["/init.sh"]

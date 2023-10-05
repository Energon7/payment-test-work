FROM nginx:alpine

# set main params
ARG BUILD_ARGUMENT_ENV=dev
ENV ENV=$BUILD_ARGUMENT_ENV

RUN ln -sf /dev/stdout /var/log/nginx/access.log && \
    ln -sf /dev/stderr /var/log/nginx/error.log && \
    rm -rf /etc/nginx/conf.d/*

# install openssl
RUN apk add --update openssl && \
    rm -rf /var/cache/apk/*

# create folder for certificates
#RUN mkdir -p /etc/nginx/certificates

# generate certificates
#RUN openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/nginx/certificates/key.pem -out /etc/nginx/certificates/cert.pem -subj "/C=AT/ST=Vienna/L=Vienna/O=Security/OU=Development/CN=example.com"

# Add httpasswd file for closing redis GUI on staging
RUN if [ "$BUILD_ARGUMENT_ENV" = "staging" ]; \
     then apk add apache2-utils && htpasswd -b -c /etc/nginx/.httpasswd stage K9olL*2Q; \
    fi
# put nginx config
COPY ./$BUILD_ARGUMENT_ENV/nginx.conf /etc/nginx/conf.d/default.conf

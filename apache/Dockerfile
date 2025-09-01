FROM httpd:2.4-alpine

# Copy the vhost.conf file into the container
COPY vhost.conf /usr/local/apache2/conf/extra/vhost.conf

# Include the custom virtual host configuration in the main Apache config
RUN echo "Include conf/extra/vhost.conf" >> /usr/local/apache2/conf/httpd.conf

# Enable mod_rewrite and mod_proxy_fcgi for PHP-FPM
RUN sed -i 's/^#LoadModule\ rewrite_module/LoadModule\ rewrite_module/' /usr/local/apache2/conf/httpd.conf \
    && sed -i 's/^#LoadModule\ proxy_module/LoadModule\ proxy_module/' /usr/local/apache2/conf/httpd.conf \
    && sed -i 's/^#LoadModule\ proxy_fcgi_module/LoadModule\ proxy_fcgi_module/' /usr/local/apache2/conf/httpd.conf
# Use the official Jenkins image as a base
FROM jenkins/jenkins:latest

# Set environment variables
ENV JENKINS_HOME /var/jenkins_home

# Copy Jenkins configurations from a backup
COPY backup/* $JENKINS_HOME/

# Switch to root user to install additional packages
USER root

# Install tini
RUN apt-get update && \
    apt-get install -y tini && \
    rm -rf /var/lib/apt/lists/*

# Switch back to the Jenkins user
USER jenkins

# Set the default command to start Jenkins using tini
ENTRYPOINT ["/usr/bin/tini", "--", "/usr/local/bin/jenkins.sh"]

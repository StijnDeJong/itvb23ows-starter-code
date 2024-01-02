// Jenkinsfile (Declarative Pipeline)
/* Requires the Docker Pipeline plugin */
pipeline {
    agent any

    stages {
        stage('Build') {
            steps {
                // Your build steps
                echo "Building..."
            }
        }

        stage('Test') {
            steps {
                // Your testing steps
                echo "Testing..."
            }
        }

        stage('Deploy') {
            steps {
                // Use Docker Compose with Docker Pipeline plugin
                echo "Deploying..."
                script {
                    docker.image('docker/compose:latest').inside('-v /var/run/docker.sock:/var/run/docker.sock') {
                        sh 'docker-compose up -d'
                    }
                }
            }
        }
    }
}

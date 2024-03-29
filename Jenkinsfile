// // Jenkinsfile (Declarative Pipeline)
// /* Requires the Docker Pipeline plugin */

// // @Library('scripts@main') _

// pipeline {
//     agent {
//         docker {
//             image 'docker:20.10'
//         }
//     }

//     stages {
//         stage('Build') {
//             steps {
//                 // Your build steps
//                 echo "Building..."
//             }
//         }

//         stage('Test') {
//             steps {
//                 // Your testing steps
//                 echo "Testing..."
//             }
//         }

//         stage('Sonarqube analysis...') {
//             steps {
//                 // Your testing steps
//                 echo "Sonarqube analysis..."
//                 script { scannerHome = tool 'SonarQube Scanner' }
//                 withSonarQubeEnv('SonarQubeServer') {
//                     sh """ 
//                         ${scannerHome}/bin/sonar-scanner -Dsonar.projectKey=project1 -Dsonar.login=sqa_14d0156c9d24f5fe9fe35d1f30b615bc44ebd9ec -Dsonar.host.url=http://host.docker.internal:9000
//                     """
//                 }
//             }
//         }

//         stage('Deploy') {
//             steps {
//                 // Use Docker Compose with Docker Pipeline plugin
//                 echo "Deploying..."
//                 // script {
//                 //     // dockerDeploy()
//                 // }
//             }
//         }
//     }
// }

pipeline{
    agent { label '!windows' }
    stages {
        stage('Build') {
            steps {
                echo "Building..."
                // Clone your source code repository
                git branch: 'main', url: 'https://github.com/StijnDeJong/itvb23ows-starter-code.git'
                // Build Docker images if necessary
                sh 'docker build -t hive hive/.'
            }
        }
        stage('Testing') {
            agent {
                docker {
                    // Use the Docker image with your unit tests environment
                    image 'hive'
                    // Mount the Docker socket for access to other containers
                    args '-v /var/run/docker.sock:/var/run/docker.sock'
                }
            }
            steps {
                echo "Testing..."
                script {
                    // Run PHPUnit tests within the 'hive' container
                    docker.image('hive').inside {
                        sh 'vendor/bin/phpunit'
                    }
                }
            }
        }
        stage('SonarQube') {
            steps {
                echo "Sonarqubing..."
                script { scannerHome = tool 'SonarQube Scanner' }
                withSonarQubeEnv('SonarQubeServer') {
                    sh """
                        ${scannerHome}/bin/sonar-scanner \
                        -Dsonar.projectKey=project1 \
                        -Dsonar.host.url=http://host.docker.internal:9000 \
                        -Dsonar.login=sqa_14d0156c9d24f5fe9fe35d1f30b615bc44ebd9ec \
                    """                
                        // -Dsonar.sources=hive
                }
            }
        }
    }
}


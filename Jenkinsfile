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
        stage('SonarQube') {
            steps{    
                script { scannerHome = tool 'SonarQube Scanner' }
                withSonarQubeEnv('SonarQubeServer') {
                    sh "${scannerHome}/bin/sonar-scanner -Dsonar.projectKey=project1 -Dsonar.host.url=http://host.docker.internal:9000 -Dsonar.login=admin -Dsonar.password=root"
                }
            }
        }
    }
}
docker run --rm -e SONAR_HOST_URL="http://sonarqube:9000" -e SONAR_SCANNER_OPTS="-Dsonar.projectKey=project1" -e SONAR_TOKEN="sqa_14d0156c9d24f5fe9fe35d1f30b615bc44ebd9ec" -v ".:/usr/src" --network jenkins sonarsource/sonar-scanner-cli


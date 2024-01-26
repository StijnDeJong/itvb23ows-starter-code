def call() {
    def PROJECT_KEY = 'project1'
    def SONAR_SCANNER_IMAGE = 'sonarsource/sonar-scanner-cli:latest'
    def scannerHome = tool 'SonarQube Scanner'
    def SONAR_TOKEN = credentials('SONAR_TOKEN')

    docker.image(SONAR_SCANNER_IMAGE).inside {
        withCredentials([string(credentialsId: 'SONAR_TOKEN', variable: 'SONAR_TOKEN')]) {
            sh """ 
                ${scannerHome}/bin/sonar-scanner -Dsonar.projectKey=project1 -Dsonar.login=sqa_14d0156c9d24f5fe9fe35d1f30b615bc44ebd9ec -Dsonar.host.url=http://host.docker.internal:9000
            """
        }
    }
}

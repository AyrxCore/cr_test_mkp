pipeline {
    agent any

    parameters {
        string(name: 'BRANCH',  defaultValue: "main", description: 'Git ref (branch or tag) to build')
        //gitParameter (branchFilter: 'origin/(.*)', defaultValue: 'main', name: 'MY_BRANCH', type: 'PT_BRANCH_TAG')
    }

    stages {
        stage('Docker login data from AWS'){

            agent {
                docker {
                    image 'amazon/aws-cli'
                    args '--entrypoint ""'
                    reuseNode true
                }
            }

            steps {
                script {
                    withAWS(credentials: 'AWS_ECR', region: '${REGION}') {
                        sh 'aws ecr get-login-password --region ${REGION} > aws-docker-creds'
                    }
                }
            }
        }

        stage('Docker build and push'){

            agent {
                docker {
                    image 'docker:20.10.14'
                    reuseNode true
                }
            }

            environment {
                DOCKER_BUILDKIT = '1'
            }

            steps {
                script{
                    sh 'env'
                    withCredentials([string(credentialsId: 'GITHUB_TOKEN', variable: 'GITHUB_TOKEN')]) {
                        sh '''
                            export COMPOSER_AUTH="{\\"github-oauth\\":{\\"github.com\\":\\"${GITHUB_TOKEN}\\"}}"
                            docker build -t ${QANTIS_REGISTRY_URL}/marketplace-nginx:${BRANCH} --build-arg COMPOSER_AUTH="${COMPOSER_AUTH}" --target nginx .
                            docker build -t ${QANTIS_REGISTRY_URL}/marketplace-php:${BRANCH} --build-arg COMPOSER_AUTH="${COMPOSER_AUTH}" --target php .
                        '''
                    }

                    sh 'cat aws-docker-creds | docker login --username AWS --password-stdin ${QANTIS_REGISTRY_URL}'
                    sh 'docker push ${QANTIS_REGISTRY_URL}/marketplace-php:${BRANCH}'
                    sh 'docker push ${QANTIS_REGISTRY_URL}/marketplace-nginx:${BRANCH}'
                }
            }
        }
    }
}

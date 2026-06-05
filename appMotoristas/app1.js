import React from 'react';
import { StyleSheet, Text, View, TextInput, TouchableOpacity, Alert } from 'react-native';

export default class App extends React.Component {
    state = {
        usuario: "mimer_v57w74h7",
        password: "b97#6gElDM"
    }

    loginDo = async(str) => {

        fetch('https://delpuente.com.gt/wp-content/plugins/manejoPedidos2/appMotoristas/val.php', {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                usuario: this.state.usuario,
                password: this.state.password,
            }),
        }).then(response => response.json())
                .then(responseJson => {
                    Alert.alert(JSON.stringify(responseJson))
                    // return responseJson.valor;
                })
                .catch(error => {
                    console.error(error);
                });




    }

    render() {
        return (
                <View style={styles.container}>
                    <Text style={styles.logo}>DelPuente</Text>
                    <View style={styles.inputView} >
                        <TextInput
                            style={styles.inputText}
                            placeholder="Usuario"
                            placeholderTextColor="#003f5c"
                            onChangeText={text => this.setState({usuario: text})}/>
                    </View>
                    <View style={styles.inputView} >
                        <TextInput
                            secureTextEntry
                            style={styles.inputText}
                            placeholder="Password..."
                            placeholderTextColor="#003f5c"
                            onChangeText={text => this.setState({password: text})}/>
                    </View>

                    <TouchableOpacity style={styles.loginBtn} onPress={ () => {
                            this.loginDo();
                                          }} >
                        <Text style={styles.loginText}>LOGIN</Text>
                    </TouchableOpacity>



                </View>
                );
    }
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: '#003f5c',
        alignItems: 'center',
        justifyContent: 'center',
    },
    logo: {
        fontWeight: "bold",
        fontSize: 50,
        color: "#fb5b5a",
        marginBottom: 40
    },
    inputView: {
        width: "80%",
        backgroundColor: "#465881",
        borderRadius: 25,
        height: 50,
        marginBottom: 20,
        justifyContent: "center",
        padding: 20
    },
    inputText: {
        height: 50,
        color: "white"
    },
    forgot: {
        color: "white",
        fontSize: 11
    },
    loginBtn: {
        width: "80%",
        backgroundColor: "#fb5b5a",
        borderRadius: 25,
        height: 50,
        alignItems: "center",
        justifyContent: "center",
        marginTop: 40,
        marginBottom: 10
    },
    loginText: {
        color: "white"
    }
});

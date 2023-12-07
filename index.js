const express = require('express');
const http = require('http');
const { Server } = require('socket.io');
const mysql = require('mysql2');
const { Socket } = require('dgram');
const { error } = require('console');

const app = express();
const server = http.createServer(app);
const io = new Server(server);

let socketIds = []; // Substitua com a inicialização real dos IDs dos sockets
let usuarios = []; // Substitua com a inicialização real dos usuários

// Configuração do banco de dados
const connection = mysql.createConnection({
  host: 'localhost',
  user: process.env.MYSQL_USER || 'root',
  password: process.env.MSQL_PASSWORD || '',
  database: 'chat_app',
});

connection.connect((err) => {
  if (err) {
    console.error('Erro ao conectar ao banco de dados: ', err);
  } else {
    console.log('Conectado ao banco de dados!');
  }
});

const path = require('path');

app.get('/', (req, res) => {
  const indexPath = path.join(__dirname, '../client/chat.html');
  console.log('Attempting to send file from path:', indexPath);
  res.sendFile(indexPath);
});

io.on('connection', (socket) => {
  // Quando um novo usuário entra
  socket.on('new user', (data) => {
    // Insere o novo usuário no banco de dados
    connection.query(
      'INSERT INTO users (nome) VALUES (?)',
      [data],
      (error, results, fields) => {
        if (error) throw error;
        // Emite uma mensagem do sistema informando que um novo usuário entrou
        io.emit('chat message', { msg: `User ${data} joined the chat`, username: 'System' });
      }
    );
  });

  // Quando uma mensagem é enviada
  socket.on('chat message', (obj) => {
    // Aqui está o trecho problemático
    connection.query(
      'INSERT INTO messages (sender_id, content) VALUES (?, ?)',
      [obj.sender_id, obj.msg],
      function (error, results, fields) {
        if (error) {
          console.error('Erro ao executar a consulta:', error);
          throw error;
        }

        // Emite o evento 'chat message' para todos os clientes
        io.emit('chat message', obj);
      }
    );
  });

  // Quando o usuário desconecta
  socket.on('disconnect', () => {
    // Obtém o índice do socket no array 'socketIds'
    let id = socketIds.indexOf(socket.id);

    // Remove o socket e o usuário correspondente dos arrays
    if (id !== -1) {
      socketIds.splice(id, 1);
      usuarios.splice(id, 1);

      // Exibe no console os arrays atualizados e uma mensagem indicando a desconexão do usuário.
      console.log(socketIds);
      console.log(usuarios);
      console.log(`User ${socket.id} disconnected.`);
    }
  });
});

server.listen(3000, () => {
  console.log('Listening on *:3000');
});

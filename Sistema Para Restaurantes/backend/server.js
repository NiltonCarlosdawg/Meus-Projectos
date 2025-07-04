const express = require("express")
const app = express()
const cors = require("cors")
const {Database} = require("sqlite3")
const nodemailer = require("nodemailer")
const dotenv = require("dotenv")
const multer = require("multer")
const path = require("path")
const bodyParser = require("body-parser")
const sqlite3 = require("sqlite3").verbose()
dotenv.config()
app.use(cors())
app.use(express.json())
app.use(bodyParser.json())
app.use("/uploads", express.static(path.join(__dirname, "uploads")))

//configurar multer para salvar imagens na pasta "uploads"
const storage = multer.diskStorage({
  destination: (req, file, cb)=>{
    cb(null, "uploads/")
  },
  filename:(req, file, cb)=>{
    cb(null, `${Date.now()}-${file.originalname}`)
  },
})
const upload = multer({ storage })


const transporter = nodemailer.createTransport({
  service: "gmail", 
  auth: {
    user: process.env.EMAIL_USER,
    pass: process.env.EMAIL_PASS,
  },
})


//Funcao para enviar o email de verificacao
const sendEmail = (to, token)=>{
  const verificationLink = `http://localhost:5173/verify-email?token=${token}`
  const mailOptions = {
    from: process.env.EMAIL_USER,
    to,
    subject: "Email Verification",
    text: `Por favor verifica o teu email clicando no link seguinte: ${verificationLink}`,
    html: `<p>Please verify your email by clicking on the following link: <a href="${verificationLink}">${verificationLink}</a></p>`
  }
  return transporter.sendMail(mailOptions)
}

//Rota pra enviar o email de verificacao

app.post('/send-verification-email', (req, res)=>{
  const {email} = req.body
  // para gerar um token de 
  const token = Math.random().toString(36).substring(2) // simple token

  sendEmail(email, token).then(()=>{
    res.status(200).send('verification email sent!')
  }).catch((error)=>{
    console.error('error sending email: ', error)
    res.status(500).send('error sending verification email')
  })
})

//logica para verificar o email

app.get('/verify-email', (req, res)=>{
  const { token } = req.query

  if(token){
    res.send('email verified sucessfully')

  }else{
    res.status(400).send('invalid token')
  }
})






let db = new sqlite3.Database("restaurant.db", (err)=>{
  if(err){
    console.log(err.message)
  }
  else{
    console.log("Ligado a database")
    db.run(
      `
      CREATE TABLE IF NOT EXISTS user(
      id_user INTEGER PRIMARY KEY,
      nome_user TEXT NOT NULL,
      email_user TEXT NOT NULL UNIQUE,
      passe_user PASSWORD NOT NULL
      )
      `,
      (err)=>{
        if(err){
          console.log("Erro ao criar usuario", err.message)
        }
        else{
          console.log("Usuario criado com sucesso")
          // db.run(`DELETE restaurant`)
        }
      }
    )
  }
})





app.post("/setuser", (req, res)=>{
  const {nome_user, email_user, passe_user} = req.body
  const query = `INSERT INTO user(nome_user, email_user, passe_user) VALUES (?, ?, ?)`
  db.run(query, [nome_user, email_user, passe_user], (err)=>{
    if(err){
      console.log(err.message)
      return res.json({err:err.message})
    }
    return res.json({mensagem:"Dados inseridos com sucesso!"})
  })
})
app.get("/getuser", (req, res)=>{
  const query = `SELECT * FROM user`
  db.all(query, (err, rows)=>{
    if(err){
      res.json({err:err.message})
    }
    res.json({rows})
  })
})

db.run(
  `
  CREATE TABLE IF NOT EXISTS comida(
  id_comida INTEGER PRIMARY KEY,
  nome_comida TEXT NOT NULL,
  filepath TEXT NOT NULL,
  info_comida TEXT NOT NULL,
  type_comida TEXT NOT NULL
  )
  `, (err)=>{
    if(err){
      console.log("Erro ao criar table comida", err.message)
    }
    else{
      console.log("Table comida criada com sucesso")
      // db.run(`DROP TABLE comida`)
    }
  }
)
//rota para upload de fotos
app.post("/setcomida", upload.single("photo") ,(req, res)=>{
  const {nome_comida,info_comida, type_comida} = req.body
  const filepath = req.file.path;
  const query = `INSERT INTO comida(nome_comida, filepath ,info_comida, type_comida) VALUES (?, ?, ?, ?)`
  db.run(query, [nome_comida, filepath,info_comida, type_comida], (err)=>{
    if(err){
      console.log(err.message)
      return res.status(500).json({err:err.message})
    }
    return res.status(201).json({mensagem:"Dados Inseridos com sucesso"})
  })
})
app.get("getcomida", (req, res)=>{
  const query = `SELECT * FROM comida`
  db.all(query, (err, rows)=>{
    if(err){
      res.status(500).json({err:err.message})
    }
    res.status(200).json({rows})
  })
})

db.run(`
  CREATE TABLE IF NOT EXISTS reserva(
  id_reserva INTEGER PRIMARY KEY,
  nome_reserva TEXT NOT NULL,
  phone_reserva INTEGER NOT NULL,
  email_reserva TEXT NOT NULL,
  data_reserva DATE NOT NULL,
  hora_reserva TEXT NOT NULL,
  pessoa_reserva TEXT NOT NULL,
  ocasion_reserva TEXT NOT NULL
  )
  `, (err)=>{
    if(err){
      console.log("erro ao criar table reserva")
    }
    else{
      console.log("Table reserva criada com sucesso")
      // db.run("DROP TABLE reserva")
    }
  })
app.post("/setreserva", (req, res)=>{
  const {nome_reserva, phone_reserva, email_reserva, data_reserva, hora_reserva, pessoa_reserva, ocasion_reserva} = req.body
  const query = `INSERT INTO reserva(nome_reserva, phone_reserva, email_reserva, data_reserva, hora_reserva, pessoa_reserva, ocasion_reserva) VALUES (?,?,?,?,?,?,?)`
  db.run(query, [nome_reserva, phone_reserva, email_reserva, data_reserva, hora_reserva, pessoa_reserva, ocasion_reserva], (err)=>{
    if(err){
      console.log(err.message)
      return res.json({err:err.message})
    }
    return res.json({mensagem:"Reserva adicionada com sucesso"})
  })
})
app.get("/getreserva", (req, res)=>{
  const query = `SELECT * FROM reserva`
  db.all(query, (err, rows)=>{
    if(err){
      res.json({err:err.message})
    }
    res.json({rows})
  })
})
app.delete('/delete/:id_reserva', (req, res)=>{
  const id_reserva = req.params.id_reserva
  const query = `DELETE FROM reserva WHERE id_reserva = ?`
  db.run(query, [id_reserva], (err)=>{
    if(err){
      console.log(err.message)
      return res.status(500).json({err:err.message})
    }
    return res.status(200).json({mensagem:"Reserva cancelada com sucesso"})
  })
})
app.delete("/deleteuser/:id_user", (req, res)=>{
  const id_user = req.params.id_user
  const query = `DELETE FROM user WHERE id_user = ?`
  db.run(query, [id_user], (err)=>{
    if(err){
      console.log(err.message)
      return res.status(500).json({err:err.message})
    }
    return res.status(200).json({mensagem:"Usuario eliminado com sucesso"})
  })
})
app.put("/edituser/:id_user", (req, res)=>{
  const id_user = req.params.id_user
  const {nome_user, email_user} = req.body
  const query = `UPDATE user SET nome_user = ?, email_user = ? WHERE id_user = ?`
  db.all(query, [nome_user, email_user], (err)=>{
    if(err){
      console.log(err.message)
      return res.status(500).json({err:err.message})
    }
    return res.status(200).json({message:"Usuario editado com sucesso"})
  })
})

const port = 3000
app.listen(port, ()=>{
  console.log(`O servidor esta a rodar na porta ${port}`)
})
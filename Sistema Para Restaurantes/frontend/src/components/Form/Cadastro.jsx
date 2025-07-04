import { useEffect, useState } from "react"
import { useNavigate } from "react-router-dom"
import Message from "../Message"
import axios from "axios"

const Cadastro = () => {
    const navigate = useNavigate()
    const [nome_user, setNome] = useState("")
    const [lastname, setLastName] = useState("")
    const [email_user, setEmail] = useState("")
    const [passe_user, setPasse] = useState("")
    const [cpasse, setCPasse] = useState("")
    const [showmessage, setshowMessage] = useState(false)
    const [message, setMessage] = useState("")
    const [type, setType] = useState(false)
    const mymessage =(m, t)=>{
      setMessage(m)
      setType(t)
      setshowMessage(true)
      setTimeout(() => {
        setshowMessage(false)
      }, 3000);
    }
    const submit =async(e)=>{
      e.preventDefault()
      try {
        if(nome_user === "" || lastname === "" || email_user === "" || passe_user === "" || cpasse === ""){
          let m = "Preencha todas as caixas"
          let t = false
          mymessage(m, t)
        }
        else{
          if(nome_user.length < 5 || lastname < 5 || email_user < 5){
            let m = "Apenas acima de 5 caracteres"
            let t = false
            mymessage(m, t)
          }
          else{
            if(passe_user !== cpasse){
              let m = "As passes não condizem"
              let t = false
              mymessage(m, t)
            }
            else{
              setNome(nome_user + ` ${lastname}`)
              const response = await axios.post('http://localhost:3000/setuser', {nome_user, email_user, passe_user})
              console.log(response.data)
              let m = "Cadastrado com sucesso"
              let t = true
              mymessage(m, t)
              navigate("/login")
            }
          }
        }
      } catch (error) {
        console.log(error)
      }
    }
   

  return (
    <div className="flex bg-myColor max-[980px]:items-center max-[980px]:justify-center">
      {showmessage&&
        <Message message={message} type={type}/>
      }
      <div className="mt-32 max-[980px]:mb-32 text-center basis-[60%] px-[6%] max-[980px]:px-[8%] max-[980px]:basis-[100%]">
        <h2 className="text-center font-bold text-[30px] uppercase my-3">Crie a tua conta!</h2>
        <form className="flex flex-col justify-center items-center gap-3">
          <div className="flex items-center justify-center gap-4 w-full">
            <input type="text" id="name" placeholder="Insira o seu nome" className="border border-[#80808056] rounded w-full py-1.5 text-[13px] px-3" onChange={(e)=>setNome(e.target.value)}/>
            <input type="text" id="lastnome" placeholder="Insira o seu ultimo nome" className="border border-[#80808056] rounded w-full py-1.5 text-[13px] px-3" onChange={(e)=>setLastName(e.target.value)}/>
          </div>
          <input type="email" name="email" id="email" placeholder="Insira o seu email" className="border border-[#80808056] rounded w-full py-1.5 text-[13px] px-3" onChange={(e)=>setEmail(e.target.value)}/>
          <input type="password" name="passe" id="passe" placeholder="Insira a passe" className="border border-[#80808056] rounded w-full py-1.5 text-[13px] px-3" onChange={(e)=>setPasse(e.target.value)}/>
          <input type="password" name="cpasse" id="cpasse" placeholder="Confirme a passe" className="border border-[#80808056] rounded w-full py-1.5 text-[13px] px-3" onChange={(e)=>setCPasse(e.target.value)}/>
          <button className="px-2 w-full rounded active:scale-[1.2] hover:scale-[1.2] transition py-2 bg-[black] text-myColor font-bold text-[13px]" onClick={submit}>Cadastrar</button>
          <div className="text-[13px] font-[quick]">Tens uma conta? <button className="cursor-pointer hover:underline" onClick={()=>navigate("/login")}>faz o Login</button></div>
        </form>
      </div>
      <img src="food (12).jpg" alt="" className="h-[658px] w-[60%] max-[980px]:hidden"/>
    </div>
  )
}

export default Cadastro
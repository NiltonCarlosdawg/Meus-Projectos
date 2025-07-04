import axios from "axios"
import { useEffect, useState } from "react"
import { useNavigate } from "react-router-dom"
import Message from "../Message"

const Login = () => {
  const navigate = useNavigate()
  const [email_user, setEmail] = useState("")
  const [passe_user, setPasse] = useState("")
  const [database, setDatabase] = useState([])
  const [showMessage, setshowMessage] = useState(false)
  const [message, setMessage] = useState("")
  const [type, setType] = useState(false)
  useEffect(()=>{
    const pegar=async()=>{
      try {
        const response = await axios.get("http://localhost:3000/getuser")
        console.log(response.data)
        setDatabase(response.data.rows)
      } catch (error) {
        console.log(error)
      }
    }
    pegar()
  }, [])
  const entrar =(e)=>{
    e.preventDefault()
    database.forEach((item)=>{
      if(item.email_user != email_user || item.passe_user != passe_user){
        setMessage("Email ou passe erradas")
        setType(false)
        setshowMessage(true)
        setTimeout(() => {
          setshowMessage(false)
        }, 3000);
      }
      else{
        setMessage("Login Effectuado com sucesso")
        setType(true)
        setshowMessage(true)
        setTimeout(() => {
          navigate("/")
        }, 500);
        setTimeout(() => {
          setshowMessage(false)
        }, 3000);
        localStorage.setItem("user", item.email_user)
      }
    })
    
  }
  return (
    <div className="flex bg-myColor max-[980px]:items-center max-[980px]:justify-center">
      {
        showMessage&&
        <Message message={message} type={type}/>
      }
      <div className="mt-32 max-[980px]:mb-32 text-center basis-[60%] px-[6%] max-[980px]:basis-[100%]">
        <h2 className="text-center font-bold text-[30px] uppercase my-3">Entre na sua conta!</h2>
        <form action="" className="flex flex-col justify-center items-center gap-3">
        <input type="email" name="email" id="email" placeholder="Insira o seu email" className="border border-[#80808056] rounded w-full py-1.5 text-[13px] px-3" onChange={(e)=>setEmail(e.target.value)}/>
        <input type="password" name="passe" id="passe" placeholder="Insira a passe" className="border border-[#80808056] rounded w-full py-1.5 text-[13px] px-3" onChange={(e)=>setPasse(e.target.value)}/>
          <button className="px-2 w-full rounded active:scale-[1.2] hover:scale-[1.2] transition py-2 bg-[black] text-myColor font-bold text-[13px]" onClick={entrar}>Login</button>
          <div className="text-[13px] font-[quick]">não tens uma conta? <button onClick={()=>navigate("/cadastro")} className="cursor-pointer hover:underline">faz o cadastro</button></div>
        </form>
      </div>
      <img src="food (16).jpg" alt="" className="h-[658px] w-[60%] max-[980px]:hidden"/>
    </div>
  )
}

export default Login
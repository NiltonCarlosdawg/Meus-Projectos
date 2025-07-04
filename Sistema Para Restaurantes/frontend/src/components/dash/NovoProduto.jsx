import axios from "axios"
import { useState } from "react"
import { BiX } from "react-icons/bi"

const NovoProduto = ({setNewProd}) => {
  const [nome_comida, setComida] = useState("")
  const [filepath, setPhoto] = useState("")
  const [info_comida, setInfo] = useState("")
  const [type_comida, setTypeComida] = useState("")
  const upload = async(e)=>{
    e.preventDefault();
    const formData = new FormData();
    formData.append("nome_comida", nome_comida)
    formData.append("filepath", filepath)
    formData.append("info_comida", info_comida)
    formData.append("type_comida", type_comida)
    try {
      const resposta = await axios.post("http://localhost:3000/setcomida", formData,{headers: {"Content-Type": "multipart/form-data"}})
      console.log(resposta.data)
    } catch (error) {
      console.log(error)
    }
  }
  return (
    <div className="backdrop-blur-sm z-[2] items-center flex justify-center fixed top-0 right-0 w-full h-full">
 
      <div className="bg-[#fefefe] w-[40%] h-[500px] p-5 rounded shadow-2xl">
      <BiX size={30} color="red" className=" cursor-pointer" onClick={()=>setNewProd(false)}/>
        <h2 className="font-bold  text-center mt-10">Adicione um produto no menu</h2>
        <form action="" onSubmit={upload} className="flex items-center flex-col justify-center gap-2 my-10">
          <input type="text" placeholder="nome" className="border-[#80808056] border-[1px] p-2 rounded shadow w-[80%] placeholder:text-[14px]" onChange={(e)=>setComida(e.target.value)}/>
          <label htmlFor="file" className="font-bold text-[13px]">Adicione uma imagem</label>
          <input type="file" placeholder="Adicione uma imagem" id="file" className="border-[#80808056] border-[1px] p-2 rounded shadow w-[80%] placeholder:text-[14px]" accept="image/*" onChange={(e)=>setPhoto(e.target.files[0])}/>
          <textarea name="info" id="info" placeholder="Informacao" className="border-[#80808056] border-[1px] p-2 rounded shadow w-[80%] placeholder:text-[14px]" onChange={(e)=>setInfo(e.target.value)}></textarea>
          <select className="border-[#80808056] border-[1px] p-2 rounded shadow w-[80%] placeholder:text-[14px]" onChange={(e)=>setTypeComida(e.target.value)}>
            <option value="Jantar">Jantar</option>
            <option value="Lanche">Lanche</option>
            <option value="Especial">Especial</option>
          </select>
          <button type="submit" className="p-2 bg-myColor text-[13px] font-bold rounded w-[80%] shadow active:scale-[1.2]">Adicionar</button>
        </form>
      </div>
    </div>
  )
}

export default NovoProduto
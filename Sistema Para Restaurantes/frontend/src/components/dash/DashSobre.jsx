import { FaFacebook, FaInstagram, FaPinterest, FaTwitter } from "react-icons/fa"
import { people } from "../object/object"

const DashAbout = () => {
  return (
    <div className=" px-[6%] mb-[350px] mt-16 max-[670px]:text-center">
      <h1 className="text-[40px] font-bold w-[600px] max-[670px]:text-[30px] max-[670px]:w-full">Somos o restaurante Mon Soir localizados na Vila alice ao lado do colegio bem dizer</h1>
      <p className="text-[13px] w-[600px] max-[670px]:w-full">Lorem ipsum dolor, sit amet consectetur adipisicing elit. Assumenda aut cum ipsa, consequuntur voluptates laborum voluptatem quam dolorum officia cumque quae impedit quaerat ex odio natus repudiandae est animi corporis?Lorem ipsum dolor, sit amet consectetur adipisicing elit. Assumenda aut cum ipsa, consequuntur voluptates laborum voluptatem quam dolorum officia cumque quae impedit quaerat ex odio natus repudiandae est animi corporis?</p>
      <button className="text-[13px] font-bold py-2 px-16 bg-[black] text-myColor rounded mt-4 mb-8 hover:scale-[1.3] transition">Contactar</button>
      <h3 className="text-[25px] font-bold text-center uppercase">Siga-nos nas nossas redes sociais</h3>
      <div className="text-[40px] flex items-center justify-center gap-8 my-8">
        <div>
          <FaFacebook className="cursor-pointer hover:scale-[1.3] transition"/>
        </div>
        <div>
          <FaInstagram className="cursor-pointer hover:scale-[1.3] transition"/>
        </div>
        <div>
          <FaTwitter className="cursor-pointer hover:scale-[1.3] transition"/>
        </div>
        <div>
          <FaPinterest className="cursor-pointer hover:scale-[1.3] transition"/>
        </div>
      </div>
      <h2 className="text-[30px] font-bold my-4">Nosso Team</h2>
      <div className="grid grid-cols-3 gap-4 max-[980px]:grid-cols-1 max-[980px]:px-[10%]">
        {
          people.map((item, index)=>{
            return(
              <div key={index} className="bg-white p-4 rotate-1 hover:rotate-3 transition">
                <img src={item.link} alt="" className=""/>
                <div>
                  <h2 className="font-bold text-[20px] mt-3">{item.nome}</h2>
                  <p>{item.role}</p>
                  <p className="text-[13px] text-gray-500">{item.info}</p>
                </div>
              </div>
            )
          })
        }
      </div>
    </div>
  )
}

export default DashAbout